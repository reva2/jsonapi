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

use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;
use Neomerx\JsonApi\Document\Error;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Reva2\JsonApi\Contracts\Decoders\CallbackResolverInterface;
use Reva2\JsonApi\Contracts\Decoders\DataParserInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\ClassMetadataInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\DocumentMetadataInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\Factory\MetadataFactoryInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\ObjectMetadataInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\PropertyMetadataInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\ResourceMetadataInterface;
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
     * @var MetadataFactoryInterface
     */
    protected $factory;

    /**
     * @var PropertyAccessor
     */
    protected $accessor;

    /**
     * @var CallbackResolverInterface
     */
    protected $callbackResolver;

    /**
     * Constructor
     *
     * @param MetadataFactoryInterface $factory
     * @param CallbackResolverInterface $callbackResolver
     */
    public function __construct(MetadataFactoryInterface $factory, CallbackResolverInterface $callbackResolver)
    {
        $this->factory = $factory;
        $this->callbackResolver = $callbackResolver;
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
    public function parseRaw($data, $path)
    {
        $this->setPath($path);

        $pathValue = null;
        if ($this->hasValue($data, $path)) {
            $pathValue = $this->getValue($data, $path);
        }

        $this->restorePath();

        return $pathValue;
    }

    /**
     * @inheritdoc
     */
    public function parseCallback($data, $path, $callback)
    {
        $this->setPath($path);

        $pathValue = null;
        if ($this->hasValue($data, $path)) {
            $pathValue = call_user_func($callback, $this->getValue($data, $path));
        }

        $this->restorePath();

        return $pathValue;
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
            if (null !== $value) {
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
    public function parseArray($data, $path, \Closure $itemsParser)
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
                    $arrayPath = sprintf("[%s]", $key);

                    $pathValue[$key] = $itemsParser($value, $arrayPath, $this);
                }
            }
        }

        $this->restorePath();

        return $pathValue;
    }

    /**
     * Parse data object value at specified path as object of specified class
     *
     * @param array|object $data
     * @param string $path
     * @param string $objType
     * @return null
     */
    public function parseObject($data, $path, $objType)
    {
        $this->setPath($path);

        $pathValue = null;
        if ((true === $this->hasValue($data, $path)) &&
            (null !== ($value = $this->getValue($data, $path)))
        ) {
            $this->restorePath();

            $pathValue = $this->parseObjectValue($value, $objType);
        }

        return $pathValue;
    }

    /**
     * @inheritdoc
     */
    public function parseResource($data, $path, $resType)
    {
        $this->setPath($path);

        $pathValue = null;
        if ((true === $this->hasValue($data, $path)) &&
            (null !== ($value = $this->getValue($data, $path)))
        ) {
            $metadata = $this->factory->getMetadataFor($resType);
            /* @var $metadata ResourceMetadataInterface */

            $discClass = $this->getClassByDiscriminator($metadata, $value);
            if ((null !== $discClass) && ($discClass !== $resType)) {
                $this->restorePath();

                return $this->parseResource($data, $path, $discClass);
            }

            $name = $this->parseString($value, 'type');
            if ($name !== $metadata->getName()) {
                throw new \InvalidArgumentException(
                    sprintf("Value must contain resource of type '%s'", $metadata->getName()),
                    409
                );
            }

            $objClass = $metadata->getClassName();
            $pathValue = new $objClass();

            if (null !== ($idMetadata = $metadata->getIdMetadata())) {
                $this->parseProperty($value, $pathValue, $idMetadata);
            }

            foreach ($metadata->getAttributes() as $attribute) {
                $this->parseProperty($value, $pathValue, $attribute);
            }

            foreach ($metadata->getRelationships() as $relationship) {
                $this->parseProperty($value, $pathValue, $relationship);
            }
        }

        $this->restorePath();

        return $pathValue;
    }

    /**
     * @inheritdoc
     */
    public function parseDocument($data, $docType)
    {
        try {
            $this->initPathStack();

            $metadata = $this->factory->getMetadataFor($docType);
            if (!$metadata instanceof DocumentMetadataInterface) {
                throw new \InvalidArgumentException(sprintf("Failed to parse %s as JSON API document", $docType));
            }

            /* @var $metadata \Reva2\JsonApi\Contracts\Decoders\Mapping\DocumentMetadataInterface */

            $docClass = $metadata->getClassName();
            $doc = new $docClass();

            $this->parseProperty($data, $doc, $metadata->getContentMetadata());

            return $doc;
        } catch (JsonApiException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw  $this->convertToApiException($e, 'document');
        }
    }

    /**
     * @inheritdoc
     */
    public function parseQueryParams($data, $paramsType)
    {
        try {
            $this->initPathStack();

            $query = $this->parseObjectValue($data, $paramsType);
            if (!$query instanceof EncodingParametersInterface) {
                throw new \InvalidArgumentException(sprintf(
                    "Query parameters object must implement interface %s",
                    EncodingParametersInterface::class
                ));
            }

            return $query;
        } catch (JsonApiException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw  $this->convertToApiException($e, 'query');
        }
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
            } elseif (is_numeric($value)) {
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

    /**
     * Parse property of specified object
     *
     * @param object|array $data
     * @param object $obj
     * @param PropertyMetadataInterface $metadata
     */
    private function parseProperty($data, $obj, PropertyMetadataInterface $metadata)
    {
        $path = $metadata->getDataPath();

        if (false === $this->hasValue($data, $path)) {
            return;
        }

        if ('custom' === $metadata->getDataType()) {
            $value = $this->parseCallback($data, $path, [$obj, $metadata->getDataTypeParams()]);
        } else {
            $value = $this->parsePropertyValue($data, $path, $metadata);
        }

        if (null !== ($converter = $metadata->getConverter())) {
            $callback = $this->callbackResolver->resolveCallback($converter);

            $value = call_user_func($callback, $value);
        }

        $setter = $metadata->getSetter();
        if (null !== $setter) {
            $obj->{$setter}($value);
        } else {
            $setter = $metadata->getPropertyName();
            $obj->{$setter} = $value;
        }
    }

    /**
     * Parse value of specified property
     *
     * @param object|array $data
     * @param string $path
     * @param PropertyMetadataInterface $metadata
     * @return mixed|null
     */
    private function parsePropertyValue($data, $path, PropertyMetadataInterface $metadata)
    {
        switch ($metadata->getDataType()) {
            case 'scalar':
                return $this->parseScalarValue($data, $path, $metadata->getDataTypeParams());

            case 'datetime':
                $format = $metadata->getDataTypeParams();
                if (empty($format)) {
                    $format = 'Y-m-d';
                }

                return $this->parseDateTime($data, $path, $format);

            case 'array':
                return $this->parseArrayValue($data, $path, $metadata->getDataTypeParams());

            case 'object':
                return $this->parseResourceOrObject($data, $path, $metadata->getDataTypeParams());

            case 'raw':
                return $this->parseRaw($data, $path);

            default:
                throw new \InvalidArgumentException(sprintf(
                    "Unsupported property data type '%s'",
                    $metadata->getDataType()
                ));
        }
    }

    /**
     * Parse value as JSON API resource or object
     *
     * @param object|array $data
     * @param string $path
     * @param string $objClass
     * @return mixed|null
     */
    public function parseResourceOrObject($data, $path, $objClass)
    {
        $metadata = $this->factory->getMetadataFor($objClass);

        if ($metadata instanceof ResourceMetadataInterface) {
            return $this->parseResource($data, $path, $objClass);
        } else {
            return $this->parseObject($data, $path, $objClass);
        }
    }

    /**
     * Parse value that contains JSON API object
     *
     * @param object|array $data
     * @param string $objType
     * @return mixed
     */
    public function parseObjectValue($data, $objType)
    {
        $metadata = $this->factory->getMetadataFor($objType);
        if (!$metadata instanceof ObjectMetadataInterface) {
            throw new \InvalidArgumentException('Invalid object metadata');
        }

        $discClass = $this->getClassByDiscriminator($metadata, $data);
        if ((null !== $discClass) && ($discClass !== $objType)) {
            return $this->parseObjectValue($data, $discClass);
        }

        $objClass = $metadata->getClassName();
        $obj = new $objClass();

        $properties = $metadata->getProperties();
        foreach ($properties as $property) {
            $this->parseProperty($data, $obj, $property);
        }

        return $obj;
    }

    /**
     * Parse value that contains array
     *
     * @param object|array $data
     * @param string $path
     * @param array $params
     * @return array|null
     */
    public function parseArrayValue($data, $path, array $params)
    {
        $type = $params[0];
        $typeParams = $params[1];

        switch ($type) {
            case 'scalar':
                return $this->parseArray(
                    $data,
                    $path,
                    function ($data, $path, DataParser $parser) use ($typeParams) {
                        return $parser->parseScalarValue($data, $path, $typeParams);
                    }
                );

            case 'datetime':
                $format = (!empty($typeParams)) ? $typeParams : 'Y-m-d';
                return $this->parseArray(
                    $data,
                    $path,
                    function ($data, $path, DataParser $parser) use ($format) {
                        return $parser->parseDateTime($data, $path, $format);
                    }
                );

            case 'object':
                return $this->parseArray(
                    $data,
                    $path,
                    function ($data, $path, DataParser $parser) use ($typeParams) {
                        return $parser->parseResourceOrObject($data, $path, $typeParams);
                    }
                );

            case 'array':
                return $this->parseArray(
                    $data,
                    $path,
                    function ($data, $path, DataParser $parser) use ($typeParams) {
                        return $parser->parseArrayValue($data, $path, $typeParams);
                    }
                );

            case 'raw':
                return $this->parseArray(
                    $data,
                    $path,
                    function ($data, $path, DataParser $parser) {
                        return $parser->parseRaw($data, $path);
                    }
                );

            default:
                throw new \InvalidArgumentException(sprintf(
                    "Unsupported array item type '%s' specified",
                    $type
                ));
        }
    }

    /**
     * Parse scalar value
     *
     * @param object|array $data
     * @param string $path
     * @param string $type
     * @return bool|float|int|null|string
     */
    public function parseScalarValue($data, $path, $type)
    {
        switch ($type) {
            case 'string':
                return $this->parseString($data, $path);

            case 'bool':
            case 'boolean':
                return $this->parseBool($data, $path);

            case 'int':
            case 'integer':
                return $this->parseInt($data, $path);

            case 'float':
            case 'double':
                return $this->parseFloat($data, $path);

            default:
                throw new \InvalidArgumentException(sprintf("Unsupported scalar type '%s' specified", $type));
        }
    }

    /**
     * Convert any exception to JSON API exception
     *
     * @param \Exception $e
     * @param string $objType
     * @return JsonApiException
     */
    private function convertToApiException(\Exception $e, $objType)
    {
        $status = $e->getCode();
        $message = 'Failed to parse request';
        if (empty($status)) {
            $message = 'Internal server error';
            $status = 500;
        }

        $source = null;
        switch ($objType) {
            case 'document':
                $source = ['pointer' => $this->getPath()];
                break;

            case 'query':
                $source = ['parameter' => $this->getPath()];
                break;
        }

        $error = new Error(rand(), null, $status, self::ERROR_CODE, $message, $e->getMessage(), $source);

        return new JsonApiException($error, $status, $e);
    }

    /**
     * Returns appropriate discriminator class for specified data
     *
     * @param ClassMetadataInterface $metadata
     * @param array|object $data
     * @return string|null
     */
    private function getClassByDiscriminator(ClassMetadataInterface $metadata, $data)
    {
        if (null === ($discField = $metadata->getDiscriminatorField())) {
            return null;
        }

        $discValue = $this->parseString($data, $discField->getDataPath());
        if (empty($discValue)) {
            $this->setPath($discField->getDataPath());

            throw new \InvalidArgumentException("Field value required and can not be empty", 422);
        }

        return $metadata->getDiscriminatorClass($discValue);
    }
}
