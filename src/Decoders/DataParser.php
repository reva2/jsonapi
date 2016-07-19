<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Decoders;

use Neomerx\JsonApi\Document\Error;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Reva2\JsonApi\Contracts\Decoders\DataParserInterface;
use Reva2\JsonApi\Contracts\Decoders\DecodersFactoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Data parser
 *
 * @package Reva2\JsonApi\Decoders
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class DataParser implements DataParserInterface
{
    const ERROR_CODE = 'ee2c1d49-ba40-4077-a6bb-b06baceb3e97';

    /**
     * Current path
     *
     * @var \SplStack
     */
    protected $path;

    /**
     * Resource decoders factory
     *
     * @var DecodersFactoryInterface
     */
    protected $factory;

    /**
     * @var PropertyAccessor
     */
    protected $accessor;

    /**
     * Constructor
     *
     * @param DecodersFactoryInterface $factory
     */
    public function __construct(DecodersFactoryInterface $factory)
    {
        $this->factory = $factory;
        $this->accessor = PropertyAccess::createPropertyAccessor();
        
        $this->initPathStack();
    }

    /**
     * @inheritdoc
     */
    public function setPath($path)
    {
        $this->path->push($this->preparePathSegment($path));

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function restorePath()
    {
        $this->path->pop();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPath()
    {
        $segments = [];
        foreach ($this->path as $segment) {
            $segments[] = $segment;
        }

        return '/' . implode('/', array_reverse($segments));
    }

    /**
     * @inheritdoc
     */
    public function hasValue($data, $path)
    {
        return $this->accessor->isReadable($data, $path);
    }

    /**
     * @inheritdoc
     */
    public function getValue($data, $path)
    {
        return $this->accessor->getValue($data, $path);
    }

    /**
     * @inheritdoc
     */
    public function parseString($data, $path)
    {
        $this->setPath($path);

        $pathValue = null;
        if ($this->hasValue($data, $path)) {
            $value = $this->getValue($data, $path);
            if ((null === $value) || (is_string($value))) {
                $pathValue = $value;
            } else {
                throw new \InvalidArgumentException(
                    sprintf("Value expected to be a string, but %s given", gettype($value)),
                    400
                );
            }
        }
        $this->restorePath();

        return $pathValue;
    }

    /**
     * @inheritdoc
     */
    public function parseInt($data, $path)
    {
        return $this->parseNumeric($data, $path, 'int');
    }

    /**
     * @inheritdoc
     */
    public function parseFloat($data, $path)
    {
        return $this->parseNumeric($data, $path, 'float');
    }

    /**
     * @inheritdoc
     */
    public function parseBool($data, $path)
    {
        $this->setPath($path);

        $pathValue = null;
        if ($this->hasValue($data, $path)) {
            $value = $this->getValue($data, $path);
            if ((null === $value) || (is_bool($value))) {
                $pathValue = $value;
            } elseif (is_string($value)) {
                $pathValue = (in_array($value, ['true', 'yes', 'y', 'on', 'enabled'])) ? true : false;
            } elseif (is_numeric($value)) {
                $pathValue = (bool) $value;
            } else {
                throw new \InvalidArgumentException(
                    sprintf("Value expected to be a boolean, but %s given", gettype($value)),
                    400
                );
            }
        }

        $this->restorePath();

        return $pathValue;
    }

    /**
     * @inheritdoc
     */
    public function parseDateTime($data, $path, $format = 'Y-m-d')
    {
        $this->setPath($path);

        $pathValue = null;
        if ($this->hasValue($data, $path)) {
            $value = $this->getValue($data, $path);
            if (null === $value) {
                $pathValue = $value;
            } else {
                if (is_string($value)) {
                    $pathValue = \DateTimeImmutable::createFromFormat($format, $value);
                }

                if (!$pathValue instanceof \DateTimeImmutable) {
                    throw new \InvalidArgumentException(
                        sprintf("Value expected to be a date/time string in '%s' format", $format),
                        400
                    );
                }
            }
        }

        $this->restorePath();

        return $pathValue;
    }

    /**
     * @inheritdoc
     */
    public function parseArray($data, $path, $itemsParser)
    {
        $this->setPath($path);

        $pathValue = null;
        if ($this->hasValue($data, $path)) {
            $value = $this->getValue($data, $path);
            if ((null !== $value) && (false === is_array($value))) {
                throw new \InvalidArgumentException(
                    sprintf("Value expected to be an array, but %s given", gettype($value)),
                    400
                );
            } elseif (is_array($value)) {
                $pathValue = [];
                $keys = array_keys($value);
                foreach ($keys as $key) {
                    $this->setPath($key);

                    $pathValue[$key] = $this->parseArrayItem($value, $key, $itemsParser);

                    $this->restorePath();
                }
            }
        }

        $this->restorePath();

        return $pathValue;
    }

    /**
     * @inheritdoc
     */
    public function parseResource($data, $path, $resType)
    {
        $this->setPath($path);

        $pathValue = null;
        if ($this->hasValue($data, $path)) {
            $this->setPath($path);

            $decoder = $this->factory->getResourceDecoder($resType);
            $pathValue = $decoder->decode($this->getValue($data, $path), $this);

            $this->restorePath();
        }

        return $pathValue;
    }

    /**
     * @inheritdoc
     */
    public function parseDocument($data, $docType)
    {
        try {
            $this->initPathStack();
            
            $decoder = $this->factory->getDocumentDecoder($docType);

            return $decoder->decode($data, $this);
        } catch (JsonApiException $e) {
            throw $e;
        } catch (\Exception $e) {
            $status = $e->getCode();
            $message = 'Failed to parse document';
            if (empty($status)) {
                $message = 'Internal server error';
                $status = 500;
            }

            $error = new Error(
                rand(),
                null,
                $status,
                self::ERROR_CODE,
                $message,
                $e->getMessage(),
                ['pointer' => $this->getPath()]
            );

            throw new JsonApiException($error, $status, $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function parseQueryParams($data, $paramsType)
    {
        throw new \RuntimeException('Not implemented');
    }

    /**
     * Prepare path segment
     *
     * @param string $path
     * @return string
     */
    protected function preparePathSegment($path)
    {
        return trim(preg_replace('~[\/]+~si', '/', str_replace(['.', '[', ']'], '/', (string) $path)), '/');
    }

    /**
     * Initialize stack that store current path
     */
    protected function initPathStack()
    {
        $this->path = new \SplStack();
    }

    /**
     * Parse single array item
     *
     * @param object|array $value
     * @param string $key
     * @param string|\Closure $itemsParser
     * @return mixed
     */
    protected function parseArrayItem($value, $key, $itemsParser)
    {
        $arrayPath = sprintf('[%s]', $key);

        if (is_string($itemsParser)) {
            switch ($itemsParser) {
                case 'string':
                    return $this->parseString($value, $arrayPath);

                case 'int':
                case 'integer':
                    return $this->parseInt($value, $arrayPath);

                case 'float':
                case 'double':
                    return $this->parseFloat($value, $arrayPath);

                case 'bool':
                case 'boolean':
                    return $this->parseBool($value, $arrayPath);

                case 'date':
                    return $this->parseDateTime($value, $arrayPath);

                case 'datetime':
                    return $this->parseDateTime($value, $arrayPath, 'Y-m-d H:i:s');

                case 'time':
                    return $this->parseDateTime($value, $arrayPath, 'H:i:s');

                default:
                    throw new \InvalidArgumentException(
                        sprintf("Unknown array items parser '%s' specified", $itemsParser),
                        500
                    );
            }
        } elseif ($itemsParser instanceof \Closure) {
            return $itemsParser($value, $arrayPath, $this);
        } else {
            throw new \InvalidArgumentException(
                sprintf(
                    "Array items parser must be a string or \\Closure, but %s given",
                    gettype($itemsParser)
                ),
                500
            );
        }
    }

    /**
     * Parse numeric value
     *
     * @param mixed $data
     * @param string $path
     * @param string $type
     * @return float|int|null
     */
    protected function parseNumeric($data, $path, $type)
    {
        $this->setPath($path);

        $pathValue = null;
        if ($this->hasValue($data, $path)) {
            $value = $this->getValue($data, $path);
            $rightType = ('int' === $type) ? is_int($value) : is_float($value);
            if ($rightType) {
                $pathValue = $value;
            } elseif (is_numeric($pathValue)) {
                $pathValue = ('int' === $type) ? (int) $value : (float) $value;
            } elseif (null !== $value) {
                throw new \InvalidArgumentException(
                    sprintf("Value expected to be %s, but %s given", $type, gettype($value)),
                    400
                );
            }
        }

        $this->restorePath();

        return $pathValue;
    }
}