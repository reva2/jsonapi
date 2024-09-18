<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reva2\JsonApi\Decoders\Mapping\Loader;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Reva2\JsonApi\Attributes\ApiObject;
use Reva2\JsonApi\Attributes\Attribute;
use Reva2\JsonApi\Attributes\Content;
use Reva2\JsonApi\Attributes\Document;
use Reva2\JsonApi\Attributes\Id;
use Reva2\JsonApi\Attributes\Loader;
use Reva2\JsonApi\Attributes\Metadata;
use Reva2\JsonApi\Attributes\Property;
use Reva2\JsonApi\Attributes\Relationship;
use Reva2\JsonApi\Attributes\Resource;
use Reva2\JsonApi\Attributes\VirtualAttribute;
use Reva2\JsonApi\Attributes\VirtualProperty;
use Reva2\JsonApi\Attributes\VirtualRelationship;
use Reva2\JsonApi\Contracts\Decoders\Mapping\ClassMetadataInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\DocumentMetadataInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\GenericMetadataInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\Loader\LoaderInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\ObjectMetadataInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\PropertyMetadataInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\ResourceMetadataInterface;
use Reva2\JsonApi\Decoders\Mapping\DocumentMetadata;
use Reva2\JsonApi\Decoders\Mapping\ObjectMetadata;
use Reva2\JsonApi\Decoders\Mapping\PropertyMetadata;
use Reva2\JsonApi\Decoders\Mapping\ResourceMetadata;
use RuntimeException;

class AttributeLoader implements LoaderInterface
{

    /**
     * @param ReflectionClass $class
     * @return GenericMetadataInterface
     */
    public function loadClassMetadata(ReflectionClass $class): GenericMetadataInterface
    {
        if (interface_exists('Doctrine\Persistence\Proxy')
            && $class->implementsInterface('Doctrine\Persistence\Proxy')
        ) {
            return $this->loadClassMetadata($class->getParentClass());
        }

        if (null !== ($resource = $this->getResourceAttribute($class))) {
            return $this->loadResourceMetadata($resource, $class);
        } elseif (null !== ($document = $this->getDocumentAttribute($class))) {
            return $this->loadDocumentMetadata($document, $class);
        } else {
            return $this->loadObjectMetadata($class, $this->getObjectAttribute($class));
        }
    }

    private function getResourceAttribute(ReflectionClass $class): ?Resource
    {
        $attributes = $class->getAttributes(Resource::class);
        if (count($attributes) === 0) {
            return null;
        }

        return $attributes[0]->newInstance();
    }

    private function getDocumentAttribute(ReflectionClass $class): ?Document
    {
        $attributes = $class->getAttributes(Document::class);
        if (count($attributes) === 0) {
            return null;
        }

        return $attributes[0]->newInstance();
    }

    private function getObjectAttribute(ReflectionClass $class): ?ApiObject
    {
        $attributes = $class->getAttributes(ApiObject::class);
        if (count($attributes) === 0) {
            return null;
        }

        return $attributes[0]->newInstance();
    }

    private function loadResourceMetadata(Resource $resource, ReflectionClass $class): ResourceMetadataInterface
    {
        $metadata = new ResourceMetadata($class->name);
        $metadata->setName($resource->type);
        $metadata->setLoader($resource->loader);

        $this->loadResourcePropertiesMetadata($metadata, $class);
        $this->loadResourceMethodsMetadata($metadata, $class);
        $this->loadDiscriminatorMetadata($metadata, $resource);

        return $metadata;
    }

    private function loadDocumentMetadata(Document $document, ReflectionClass $class): DocumentMetadataInterface
    {
        $metadata = new DocumentMetadata($class->name);
        $metadata->setAllowEmpty($document->allowEmpty);

        $properties = $class->getProperties();
        foreach ($properties as $property) {
            if ($property->getDeclaringClass()->name !== $class->name) {
                continue;
            }


            foreach ($property->getAttributes() as $attr) {
                if ($attr->getName() === Content::class) {
                    $metadata->setContentMetadata($this->loadPropertyMetadata($property, $attr->newInstance()));
                } elseif ($attr->getName() === Metadata::class) {
                    $metadata->setMetadata($this->loadPropertyMetadata($property, $attr->newInstance()));
                }
            }
        }

        return $metadata;
    }

    private function loadObjectMetadata(ReflectionClass $class, ?ApiObject $obj): ObjectMetadataInterface
    {
        $metadata = new ObjectMetadata($class->name);

        $this->loadObjectPropertiesMetadata($metadata, $class);
        $this->loadObjectMethodsMetadata($metadata, $class);

        if (null !== $obj) {
            $this->loadDiscriminatorMetadata($metadata, $obj);
        }

        return $metadata;
    }

    private function loadDiscriminatorMetadata(ClassMetadataInterface $metadata, ApiObject $apiObj): void
    {
        if (empty($apiObj->discField)) {
            return;
        }

        $field = $apiObj->discField;

        $properties = [];
        if ($metadata instanceof ObjectMetadataInterface) {
            $properties = $metadata->getProperties();
        } elseif ($metadata instanceof ResourceMetadataInterface) {
            $properties = $metadata->getAttributes();
        }

        $fieldMeta = (array_key_exists($field, $properties)) ? $properties[$field] : null;

        if (null === $fieldMeta) {
            throw new InvalidArgumentException(
                "Specified discriminator field not found in object properties"
            );
        }

        if (('scalar' !== $fieldMeta->getDataType()) || ('string' !== $fieldMeta->getDataTypeParams())) {
            throw new InvalidArgumentException(
                "Discriminator field must point to property that contain string value"
            );
        }

        $metadata->setDiscriminatorField($fieldMeta);
        $metadata->setDiscriminatorMap($apiObj->discMap);
        $metadata->setDiscriminatorError($apiObj->discError);
    }

    private function loadResourcePropertiesMetadata(ResourceMetadata $metadata, ReflectionClass $class): void
    {
        $properties = $class->getProperties();
        foreach ($properties as $property) {
            if ($property->getDeclaringClass()->name !== $class->name) {
                continue;
            }

            foreach ($property->getAttributes() as $attr) {
                switch ($attr->getName()) {
                    case  Attribute::class:
                        $metadata->addAttribute($this->loadPropertyMetadata($property, $attr->newInstance()));
                        break;

                    case Relationship::class:
                        $metadata->addRelationship($this->loadPropertyMetadata($property, $attr->newInstance()));
                        break;

                    case Id::class:
                        $metadata->setIdMetadata($this->loadPropertyMetadata($property, $attr->newInstance()));
                        break;
                }
            }
        }
    }

    private function loadObjectPropertiesMetadata(ObjectMetadata $metadata, ReflectionClass $class): void
    {
        $properties = $class->getProperties();
        foreach ($properties as $property) {
            if ($property->getDeclaringClass()->name !== $class->name) {
                continue;
            }

            foreach ($property->getAttributes() as $attr) {
                if ($attr->getName() === Property::class) {
                    $metadata->addProperty($this->loadPropertyMetadata($property, $attr->newInstance()));
                }
            }
        }
    }

    private function loadResourceMethodsMetadata(ResourceMetadata $metadata, ReflectionClass $class): void
    {
        $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            if ($method->getDeclaringClass()->name !== $class->name) {
                continue;
            }

            foreach ($method->getAttributes() as $attr) {
                switch ($attr->getName()) {
                    case VirtualAttribute::class:
                        $metadata->addAttribute($this->loadVirtualPropertyMetadata($method, $attr->newInstance()));
                        break;

                    case VirtualRelationship::class:
                        $metadata->addRelationship($this->loadVirtualPropertyMetadata($method, $attr->newInstance()));
                }
            }
        }
    }

    private function loadObjectMethodsMetadata(ObjectMetadata $metadata, ReflectionClass $class): void
    {
        $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            if ($method->getDeclaringClass()->name !== $class->name) {
                continue;
            }

            foreach ($method->getAttributes() as $attr) {
                if ($attr->getName() === VirtualProperty::class) {
                    $metadata->addProperty($this->loadVirtualPropertyMetadata($method, $attr->newInstance()));
                }
            }
        }
    }

    private function loadPropertyMetadata(
        ReflectionProperty $property,
        Property $attr
    ): PropertyMetadataInterface
    {
        $metadata = new PropertyMetadata($property->name, $property->class);

        list($dataType, $dataTypeParams) = $this->parseDataType($property, $attr);

        $metadata
            ->setDataType($dataType)
            ->setDataTypeParams($dataTypeParams)
            ->setDataPath($this->getDataPath($property, $attr))
            ->setConverter($attr->converter)
            ->setGroups($attr->groups)
            ->setLoaders($this->buildLoadersList($attr->loaders));

        if ($attr->setter) {
            $metadata->setSetter($attr->setter);
        } elseif (false === $property->isPublic()) {
            $setter = 'set' . ucfirst($property->name);
            if (false === $property->getDeclaringClass()->hasMethod($setter)) {
                throw new RuntimeException(sprintf(
                    "Couldn't find setter for non public property: %s:%s",
                    $property->class,
                    $property->name
                ));
            }

            $metadata->setSetter($setter);
        }

        return $metadata;
    }

    private function loadVirtualPropertyMetadata(
        ReflectionMethod $method,
        VirtualProperty $attr
    ): PropertyMetadataInterface
    {
        if (empty($attr->name)) {
            throw new InvalidArgumentException(sprintf(
                "Virtual property name not specified: %s:%s()",
                $method->class,
                $method->name
            ));
        }

        list($dataType, $dataTypeParams) = $this->parseVirtualDataType($method, $attr);

        $metadata = new PropertyMetadata($attr->name, $method->class);
        $metadata
            ->setDataType($dataType)
            ->setDataTypeParams($dataTypeParams)
            ->setDataPath($this->getVirtualDataPath($attr))
            ->setConverter($attr->converter)
            ->setGroups($attr->groups)
            ->setSetter($method->name);

        return $metadata;
    }

    private function parseDataType(ReflectionProperty $property, Property $attr): array
    {
        if (!empty($attr->parser)) {
            if (!$property->getDeclaringClass()->hasMethod($attr->parser)) {
                throw new InvalidArgumentException(sprintf(
                    "Custom parser function %s:%s() for property '%s' does not exist",
                    $property->class,
                    $attr->parser,
                    $property->name
                ));
            }
            return ['custom', $attr->parser];
        } elseif (!empty($attr->type)) {
            return $this->parseDataTypeString($attr->type, $attr->multiple);
        } elseif (($property->getType() !== null) && $property->getType()->getName() !== 'array') {
            return $this->parseDataTypeString($property->getType()->getName());
        } elseif (preg_match('~@var\s(.*?)\s~si', $property->getDocComment(), $matches)) {
            return $this->parseDataTypeString($matches[1]);
        } else {
            return ['raw', null];
        }
    }

    private function parseVirtualDataType(ReflectionMethod $method, VirtualProperty $attr): array
    {
        if (!empty($attr->parser)) {
            if (!$method->getDeclaringClass()->hasMethod($attr->parser)) {
                throw new InvalidArgumentException(sprintf(
                    "Custom parser function %s:%s() for virtual property '%s' does not exist",
                    $method->class,
                    $attr->parser,
                    $attr->name
                ));
            }
            return ['custom', $attr->parser];
        } elseif (!empty($attr->type)) {
            return $this->parseDataTypeString($attr->type);
        } else {
            return ['raw', null];
        }
    }

    private function getDataPath(ReflectionProperty $property, Property $attr): string
    {
        $propertyPath = (!empty($attr->path)) ? $attr->path : $property->name;

        $prefix = '';
        $suffix = '';
        if ($attr instanceof Attribute) {
            $prefix = 'attributes.';
        } elseif ($attr instanceof Relationship) {
            $prefix = 'relationships.';
            $suffix = '.data';
        }

        if (!empty($prefix) || !empty($suffix)) {
            return $prefix . $propertyPath . $suffix;
        }

        return $propertyPath;
    }

    private function getVirtualDataPath(VirtualProperty $attr): string
    {
        $prefix = '';
        $suffix = '';
        if ($attr instanceof VirtualAttribute) {
            $prefix = 'attributes.';
        } elseif ($attr instanceof VirtualRelationship) {
            $prefix = 'relationships.';
            $suffix = '.data';
        }

        $propertyPath = (!empty($attr->path)) ? $attr->path : $attr->name;

        if (!empty($prefix) || !empty($suffix)) {
            return $prefix . $propertyPath . $suffix;
        }

        return $propertyPath;
    }

    private function parseDataTypeString(string $type, bool $multiple = false): array
    {
        $type = mb_ltrim($type, "?");

        $params = null;

        if ($multiple) {
            $dataType = 'array';
            $params = $this->parseDataTypeString($type);
        } elseif ('raw' === $type) {
            $dataType = 'raw';
        } elseif ($this->isScalarDataType($type)) {
            $dataType = 'scalar';
            $params = $type;
        } elseif (preg_match('~^DateTime(<(.*?)>)?$~', $type, $matches)) {
            $dataType = 'datetime';
            if (3 === count($matches)) {
                $params = $matches[2];
            }
        } elseif (
            ($multiple === true) ||
            (preg_match('~Array(<(.*?)>)?$~si', $type, $matches)) ||
            (preg_match('~^(.*?)\[\]$~si', $type, $matches))
        ) {
            $dataType = 'array';
            if (3 === count($matches)) {
                $params = $this->parseDataTypeString($matches[2]);
            } elseif (2 === count($matches)) {
                $params = $this->parseDataTypeString($matches[1]);
            } else {
                $params = ['raw', null];
            }
        } else {
            $type = ltrim($type, '\\');

            if (!class_exists($type)) {
                throw new InvalidArgumentException(sprintf(
                    "Unknown object type '%s' specified",
                    $type
                ));
            }

            $dataType = 'object';
            $params = $type;
        }

        return [$dataType, $params];
    }

    private function isScalarDataType(string $type): bool
    {
        return in_array($type, ['string', 'bool', 'boolean', 'int', 'integer', 'float', 'double']);
    }

    /**
     * @param Loader[] $loaders
     * @return array
     */
    private function buildLoadersList(array $loaders): array
    {
        $list = [];

        foreach ($loaders as $loader) {
            $list[$loader->group] = $loader->loader;
        }

        return $list;
    }
}