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

use Closure;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use InvalidArgumentException;
use Neomerx\JsonApi\Schema\Error;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Reva2\JsonApi\Contracts\Decoders\CallbackResolverInterface;
use Reva2\JsonApi\Contracts\Decoders\DataParserInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\ClassMetadataInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\DocumentMetadataInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\Factory\MetadataFactoryInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\ObjectMetadataInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\PropertyMetadataInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\ResourceMetadataInterface;
use Reva2\JsonApi\Contracts\Encoder\EncodingParametersInterface;
use SplStack;
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
     * @var SplStack
     */
    protected SplStack $path;

    /**
     * @var Context
     */
    protected Context $context;

    /**
     * Resource decoders factory
     *
     * @var MetadataFactoryInterface
     */
    protected MetadataFactoryInterface $factory;

    /**
     * @var PropertyAccessor
     */
    protected PropertyAccessor $accessor;

    /**
     * @var CallbackResolverInterface
     */
    protected CallbackResolverInterface $callbackResolver;

    /**
     * Serialization groups
     *
     * @var string[]
     */
    protected array $serializationGroups = ['Default'];

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
        $this->accessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableExceptionOnInvalidIndex()
            ->getPropertyAccessor();

        $this->initPathStack();
        $this->initContext();
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setPath(string $path): self
    {
        $this->path->push($this->preparePathSegment($path));

        return $this;
    }

    /**
     * @return $this
     */
    public function restorePath(): self
    {
        $this->path->pop();

        return $this;
    }

    /**
     * @return string[]
     */
    public function getSerializationGroups(): array
    {
        return $this->serializationGroups;
    }

    /**
     * @param string[] $groups
     * @return $this
     */
    public function setSerializationGroups(array $groups): self
    {
        $this->serializationGroups = $groups;

        return $this;
    }

    public function getPath(): string
    {
        $segments = [];
        foreach ($this->path as $segment) {
            $segments[] = $segment;
        }

        return '/' . implode('/', array_reverse($segments));
    }

    /**
     * @param mixed $data
     * @param string $path
     * @return bool
     */
    public function hasValue(mixed $data, string $path): bool
    {
        return $this->accessor->isReadable($data, $path);
    }

    /**
     * @param mixed $data
     * @param string $path
     * @return mixed
     */
    public function getValue(mixed $data, string $path): mixed
    {
        return $this->accessor->getValue($data, $path);
    }

    /**
     * @param mixed $data
     * @param string $path
     * @return string|null
     */
    public function parseString(mixed $data, string $path): ?string
    {
        $this->setPath($path);

        $pathValue = null;
        if ($this->hasValue($data, $path)) {
            $value = $this->getValue($data, $path);
            if ((null === $value) || (is_string($value))) {
                $pathValue = $value;
            } else {
                throw new InvalidArgumentException(
                    sprintf("Value expected to be a string, but %s given", gettype($value)),
                    400
                );
            }
        }
        $this->restorePath();

        return $pathValue;
    }

    /**
     * @param mixed $data
     * @param string $path
     * @return int|null
     */
    public function parseInt(mixed $data, string $path): ?int
    {
        return $this->parseNumeric($data, $path, 'int');
    }

    /**
     * @param mixed $data
     * @param string $path
     * @return float|null
     */
    public function parseFloat(mixed $data, string $path): ?float
    {
        return $this->parseNumeric($data, $path, 'float');
    }

    /**
     * @param mixed $data
     * @param string $path
     * @return mixed
     */
    public function parseRaw(mixed $data, string $path): mixed
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
     * @param mixed $data
     * @param string $path
     * @param callable $callback
     * @return mixed
     */
    public function parseCallback(mixed $data, string $path, callable $callback): mixed
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
     * @param mixed $data
     * @param string $path
     * @return bool|null
     */
    public function parseBool(mixed $data, string $path): ?bool
    {
        $this->setPath($path);

        $pathValue = null;
        if ($this->hasValue($data, $path)) {
            $value = $this->getValue($data, $path);
            if ((null === $value) || (is_bool($value))) {
                $pathValue = $value;
            } elseif (is_string($value)) {
                $pathValue = in_array($value, ['true', 'yes', 'y', 'on', 'enabled']);
            } elseif (is_numeric($value)) {
                $pathValue = (bool) $value;
            } else {
                throw new InvalidArgumentException(
                    sprintf("Value expected to be a boolean, but %s given", gettype($value)),
                    400
                );
            }
        }

        $this->restorePath();

        return $pathValue;
    }

    /**
     * @param mixed $data
     * @param string $path
     * @param string $format
     * @return DateTimeInterface|null
     */
    public function parseDateTime(mixed $data, string $path, string $format = 'Y-m-d'): ?DateTimeInterface
    {
        $this->setPath($path);

        $pathValue = null;
        if ($this->hasValue($data, $path)) {
            $value = $this->getValue($data, $path);
            if (null !== $value) {
                if (is_string($value)) {
                    $pathValue = DateTimeImmutable::createFromFormat($format, $value);
                }

                if (!$pathValue instanceof DateTimeImmutable) {
                    throw new InvalidArgumentException(
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
     * @param mixed $data
     * @param string $path
     * @param Closure $itemsParser
     * @return array|null
     */
    public function parseArray(mixed $data, string $path, Closure $itemsParser): ?array
    {
        $this->setPath($path);

        $pathValue = null;
        if ($this->hasValue($data, $path)) {
            $value = $this->getValue($data, $path);
            if (false === is_array($value)) {
                throw new InvalidArgumentException(
                    sprintf("Value expected to be an array, but %s given", gettype($value)),
                    400
                );
            }

            $pathValue = [];
            $keys = array_keys($value);
            foreach ($keys as $key) {
                $arrayPath = sprintf("[%s]", $key);

                $pathValue[$key] = $itemsParser($value, $arrayPath, $this);
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
    public function parseResource($data, $path, $resType, $loader = null)
    {
        $this->setPath($path);

        $pathValue = null;
        if ((true === $this->hasValue($data, $path)) &&
            (null !== ($value = $this->getValue($data, $path)))
        ) {
            $metadata = $this->factory->getMetadataFor($resType);
            /* @var $metadata ResourceMetadataInterface */

            $name = $this->parseString($value, 'type');
            if ($name !== $metadata->getName()) {
                throw new InvalidArgumentException(
                    sprintf("Value must contain resource of type '%s'", $metadata->getName()),
                    409
                );
            }

            $id = ($this->hasValue($value, 'id')) ? $this->getValue($value, 'id') : null;

            if (null !== $id) {
                $pathValue = $this->context->getResource($name, $id);
                if (null !== $pathValue) {
                    $id = null;
                }
            }

            if (null === $pathValue && $loader === null) {
                $loader = $metadata->getLoader();
            }

            if ((null !== $loader) && (null !== $id)) {
                $callback = $this->callbackResolver->resolveCallback($loader);
                $pathValue = call_user_func($callback, $id, $metadata);
            }

            if (null === $pathValue) {
                $discClass = $this->getClassByDiscriminator($metadata, $value);
                if ((null !== $discClass) && ($discClass !== $resType)) {
                    $metadata = $this->factory->getMetadataFor($discClass);
                }

                $objClass = $metadata->getClassName();
                $pathValue = new $objClass();

                if (null !== ($idMetadata = $metadata->getIdMetadata())) {
                    $this->parseProperty($value, $pathValue, $idMetadata);
                }
            } else {
                $valueClass = get_class($pathValue);
                if ($valueClass !== $resType) {
                    $metadata = $this->factory->getMetadataFor($valueClass);
                }
            }

            if (null !== $id) {
                $this->context->registerResource($name, $id, $pathValue);
            }

            foreach ($metadata->getAttributes() as $attribute) {
                $this->parseProperty($value, $pathValue, $attribute);
            }

            foreach ($metadata->getRelationships() as $relationship) {
                $this->parseRelationship($value, $pathValue, $relationship);
            }
        }

        $this->restorePath();

        return $pathValue;
    }

    /**
     * @inheritdoc
     */
    public function parseDocument(mixed $data, string $docType): mixed
    {
        try {
            $this->initPathStack();
            $this->initContext();

            $this->parseErrors($data);

            $metadata = $this->factory->getMetadataFor($docType);
            if (!$metadata instanceof DocumentMetadataInterface) {
                throw new InvalidArgumentException(sprintf("Failed to parse %s as JSON API document", $docType));
            }

            /* @var $metadata \Reva2\JsonApi\Contracts\Decoders\Mapping\DocumentMetadataInterface */

            $docClass = $metadata->getClassName();
            $doc = new $docClass();

            $this->parseLinkedResources($data);

            $this->parseProperty($data, $doc, $metadata->getContentMetadata());

            $docMetadata = $metadata->getMetadata();
            if ($docMetadata !== null) {
                $this->parseProperty($data, $doc, $metadata->getMetadata());
            }

            return $doc;
        } catch (JsonApiException $e) {
            throw $e;
        } catch (Exception $e) {
            throw  $this->convertToApiException($e, 'document');
        }
    }

    /**
     * @inheritdoc
     */
    public function parseQueryParams(mixed $data, string $paramsType): EncodingParametersInterface
    {
        try {
            $this->initPathStack();
            $this->initContext();

            return $this->parseObjectValue($data, $paramsType);
        } catch (JsonApiException $e) {
            throw $e;
        } catch (Exception $e) {
            throw  $this->convertToApiException($e, 'query');
        }
    }

    /**
     * Prepare path segment
     *
     * @param string $path
     * @return string
     */
    protected function preparePathSegment($path): string
    {
        return trim(preg_replace('~[\/]+~si', '/', str_replace(['.', '[', ']'], '/', (string) $path)), '/');
    }

    /**
     * Initialize stack that store current path
     */
    protected function initPathStack()
    {
        $this->path = new SplStack();
    }

    /**
     * Initialize decoder context
     */
    protected function initContext()
    {
        $this->context = new Context();
    }

    /**
     * Parse numeric value
     *
     * @param mixed $data
     * @param string $path
     * @param string $type
     * @return float|int|null
     */
    protected function parseNumeric(mixed $data, string $path, string $type): mixed
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
                throw new InvalidArgumentException(
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
     * @param string|null $path
     */
    private function parseProperty($data, $obj, PropertyMetadataInterface $metadata, $path = null)
    {
        if (null === $path) {
            $path = $metadata->getDataPath();
        }

        if ((false === $this->hasValue($data, $path)) ||
            (true === $this->isExcludedProperty($metadata))
        ) {
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

        $this->setProperty($obj, $value, $metadata);
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
                return $this->parseArrayValue($data, $path, $metadata->getDataTypeParams(), $metadata);

            case 'object':
                return $this->parseResourceOrObject($data, $path, $metadata->getDataTypeParams(), $metadata);

            case 'raw':
                return $this->parseRaw($data, $path);

            default:
                throw new InvalidArgumentException(sprintf(
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
     * @param PropertyMetadataInterface $propMetadata
     * @return mixed|null
     */
    public function parseResourceOrObject(
        mixed $data,
        string $path,
        string $objClass,
        PropertyMetadataInterface $propMetadata
    ): mixed {
        $objMetadata = $this->factory->getMetadataFor($objClass);
        if ($objMetadata instanceof ResourceMetadataInterface) {
            $loader = null;
            foreach ($propMetadata->getLoaders() as $group => $groupLoader) {
                if (in_array($group, $this->serializationGroups)) {
                    $loader = $groupLoader;
                }
            }

            return $this->parseResource($data, $path, $objClass, $loader);
        }

        return $this->parseObject($data, $path, $objClass);
    }

    /**
     * Parse value that contains JSON API object
     *
     * @param object|array $data
     * @param string $objType
     * @return mixed
     */
    public function parseObjectValue(mixed $data, string $objType): mixed
    {
        $metadata = $this->factory->getMetadataFor($objType);
        if (!$metadata instanceof ObjectMetadataInterface) {
            throw new InvalidArgumentException('Invalid object metadata');
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
     * @param PropertyMetadataInterface $propMetadata
     * @return array|null
     */
    public function parseArrayValue(
        mixed $data,
        string $path,
        array $params,
        PropertyMetadataInterface $propMetadata
    ): ?array {
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
                    function ($data, $path, DataParser $parser) use ($typeParams, $propMetadata) {
                        return $parser->parseResourceOrObject($data, $path, $typeParams, $propMetadata);
                    }
                );

            case 'array':
                return $this->parseArray(
                    $data,
                    $path,
                    function ($data, $path, DataParser $parser) use ($typeParams, $propMetadata) {
                        return $parser->parseArrayValue($data, $path, $typeParams, $propMetadata);
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
                throw new InvalidArgumentException(sprintf(
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
    public function parseScalarValue(mixed $data, string $path, string $type): mixed
    {
        return match ($type) {
            'string' => $this->parseString($data, $path),
            'bool', 'boolean' => $this->parseBool($data, $path),
            'int', 'integer' => $this->parseInt($data, $path),
            'float', 'double' => $this->parseFloat($data, $path),
            default => throw new InvalidArgumentException(sprintf("Unsupported scalar type '%s' specified", $type)),
        };
    }

    /**
     * Convert any exception to JSON API exception
     *
     * @param Exception $e
     * @param string $objType
     * @return JsonApiException
     */
    private function convertToApiException(Exception $e, string $objType): JsonApiException
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

        $error = new Error(
            idx: rand(),
            status: $status,
            code: self::ERROR_CODE,
            title: $message,
            detail: $e->getMessage(),
            source: $source
        );

        return new JsonApiException($error, $status, $e);
    }

    /**
     * Returns appropriate discriminator class for specified data
     *
     * @param ClassMetadataInterface $metadata
     * @param array|object $data
     * @return string|null
     */
    private function getClassByDiscriminator(ClassMetadataInterface $metadata, mixed $data): ?string
    {
        if (null === ($discField = $metadata->getDiscriminatorField())) {
            return null;
        }

        $discValue = $this->parseString($data, $discField->getDataPath());
        if (empty($discValue)) {
            $this->setPath($discField->getDataPath());

            throw new InvalidArgumentException("Field value required and can not be empty", 422);
        }

        return $metadata->getDiscriminatorClass($discValue);
    }

    /**
     * Check if specified property should be excluded
     *
     * @param PropertyMetadataInterface $metadata
     * @return bool
     */
    private function isExcludedProperty(PropertyMetadataInterface $metadata)
    {
        $propertyGroups = $metadata->getGroups();
        foreach ($propertyGroups as $group) {
            if (in_array($group, $this->serializationGroups)) {
                return false;
            }
        }

        return true;
    }

    /**
     *
     * @param mixed $data
     */
    private function parseLinkedResources(mixed $data): void
    {
        if (false === $this->hasValue($data, 'included')) {
            return;
        }

        $linkedData = $this->getValue($data, 'included');
        if (!is_array($linkedData)) {
            return;
        }

        foreach ($linkedData as $idx => $resData) {
            $id = null;
            if ($this->hasValue($resData, 'id')) {
                $id = $this->getValue($resData, 'id');
            }

            $type = null;
            if ($this->hasValue($resData, 'type')) {
                $type = $this->getValue($resData, 'type');
            }

            if (empty($id) || empty($type) || !is_string($id) || !is_string($type)) {
                continue;
            }

            $this->context->addLinkedData($type, $id, $idx, $resData);
        }
    }

    /**
     * Parse specified relationship data
     *
     * @param mixed $data
     * @param mixed $pathValue
     * @param PropertyMetadataInterface $relationship
     * @return void
     */
    private function parseRelationship(mixed $data, mixed $pathValue, PropertyMetadataInterface $relationship): void
    {
        if ('array' === $relationship->getDataType()) {
            $this->parseArrayRelationship($data, $pathValue, $relationship);

            return;
        }

        $resType = null;
        if ($this->hasValue($data, $relationship->getDataPath() . '.type')) {
            $resType = $this->parseString($data, $relationship->getDataPath() . '.type');
        }

        $resId = null;
        if ($this->hasValue($data, $relationship->getDataPath() . '.id')) {
            $resId = $this->getValue($data, $relationship->getDataPath() . '.id');
        }

        if ((null !== $resId) &&
            (null !== $resType) &&
            (null !== ($res = $this->context->getResource($resType, $resId)))
        ) {
            $this->setProperty($pathValue, $res, $relationship);

            return;
        }


        if (null !== ($linkedData = $this->context->getLinkedData($resType, $resId))) {
            $idx = $this->context->getLinkedDataIndex($resType, $resId);
            $prevPath = $this->path;

            $this->initPathStack();
            $this->setPath('included')->setPath($idx);

            $this->parseProperty([$idx => $linkedData], $pathValue, $relationship, '[' . $idx . ']');
            $this->path = $prevPath;

            return;
        }

        $this->parseProperty($data, $pathValue, $relationship);
    }

    /**
     * Parse data for relationship that contains array of resources
     *
     * @param mixed $data
     * @param mixed $pathValue
     * @param PropertyMetadataInterface $relationship
     * @return void
     */
    private function parseArrayRelationship(mixed $data, mixed $pathValue, PropertyMetadataInterface $relationship): void
    {
        $data = $this->parseArray($data, $relationship->getDataPath(), function ($data, $path) use ($relationship) {
            $resType = null;
            if ($this->hasValue($data, $path . '.type')) {
                $resType = $this->parseString($data, $path . '.type');
            }

            $resId = null;
            if ($this->hasValue($data, $path .'.id')) {
                $resId = $this->getValue($data, $path . '.id');
            }

            if ((null !== $resType) &&
                (null !== $resId) &&
                (null !== ($parsed = $this->context->getResource($resType, $resId)))
            ) {
                return $parsed;
            }

            $params = $relationship->getDataTypeParams();

            if (null !== ($linkedData = $this->context->getLinkedData($resType, $resId))) {
                $idx = $this->context->getLinkedDataIndex($resType, $resId);

                $prevPath = $this->path;
                $this->initPathStack();
                $this->setPath('included')->setPath($idx);

                $parsed = $this->parseResourceOrObject(
                    [$idx => $linkedData],
                    '[' . $idx .']',
                    $params[1],
                    $relationship
                );

                $this->path = $prevPath;

                return $parsed;
            }

            return $this->parseResourceOrObject($data, $path, $params[1], $relationship);
        });

        if (is_array($data)) {
            $this->setProperty($pathValue, $data, $relationship);
        }
    }

    /**
     * Sets property value using metadata
     *
     * @param mixed $obj
     * @param mixed $value
     * @param PropertyMetadataInterface $metadata
     */
    private function setProperty(mixed $obj, mixed $value, PropertyMetadataInterface $metadata): void
    {
        $setter = $metadata->getSetter();
        if (null !== $setter) {
            $obj->{$setter}($value);
        } else {
            $setter = $metadata->getPropertyName();
            $obj->{$setter} = $value;
        }
    }

    /**
     * Parse errors from JSON API document
     *
     * @param object $data
     */
    private function parseErrors(mixed $data): void
    {
        if (!$this->hasValue($data, 'errors')) {
            return;
        }

        $errors = $this->parseArray($data, 'errors', function ($data, $path) {
            $source = null;
            if ($this->hasValue($data, $path . '.source.pointer')) {
                $source = ['pointer' => $this->parseString($data, $path . '.source.pointer')];
            } elseif ($this->hasValue($data, $path . '.source.parameter')) {
                $source = ['parameter' => $this->parseString($data, $path . '.source.parameter')];
            }

            $meta = $this->parseRaw($data, $path . '.meta');

            return new Error(
                idx: $this->parseString($data, $path . '.id'),
                status: $this->parseString($data, $path . '.status'),
                code: $this->parseString($data, $path . '.code'),
                title: $this->parseString($data, $path . '.title'),
                detail: $this->parseString($data, $path . '.detail'),
                source: $source,
                hasMeta: !!$meta,
                meta: $this->parseRaw($data, $path . '.meta')
            );
        });

        throw new JsonApiException($errors);
    }
}
