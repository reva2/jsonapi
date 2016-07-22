<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) OrbitScripts LLC <support@orbitscripts.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Tests\Decoders\Mapping\Loader;

use Doctrine\Common\Annotations\AnnotationReader;
use Reva2\JsonApi\Contracts\Decoders\Mapping\DocumentMetadataInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\ObjectMetadataInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\PropertyMetadataInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\ResourceMetadataInterface;
use Reva2\JsonApi\Decoders\Mapping\Loader\AnnotationLoader;
use Reva2\JsonApi\Tests\Fixtures\Documents\PetsList;
use Reva2\JsonApi\Tests\Fixtures\Objects\AnotherObject;
use Reva2\JsonApi\Tests\Fixtures\Objects\BaseObject;
use Reva2\JsonApi\Tests\Fixtures\Objects\ExampleObject;
use Reva2\JsonApi\Tests\Fixtures\Objects\InvalidObject;
use Reva2\JsonApi\Tests\Fixtures\Objects\InvalidObject2;
use Reva2\JsonApi\Tests\Fixtures\Resources\Cat;
use Reva2\JsonApi\Tests\Fixtures\Resources\Dog;
use Reva2\JsonApi\Tests\Fixtures\Resources\Pet;
use Reva2\JsonApi\Tests\Fixtures\Resources\Store;

/**
 * Test for metadata loader that use doctrine annotations
 *
 * @package Reva2\JsonApi\Tests\Decoders\Mapping\Loader
 * @author Sergey Revenko <reva2@orbita1.ru>
 */
class AnnotationLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AnnotationLoader
     */
    protected $loader;

    protected function setUp()
    {
        parent::setUp();

        $this->loader = new AnnotationLoader(new AnnotationReader());
    }

    /**
     * @test
     */
    public function shouldLoadObjectMetadata()
    {
        $class = new \ReflectionClass(ExampleObject::class);

        $metadata = $this->loader->loadClassMetadata($class);

        $this->assertInstanceOf(ObjectMetadataInterface::class, $metadata);

        $properties = $metadata->getProperties();

        $this->assertArrayHasKey('strProp', $properties);
        $prop = $properties['strProp'];
        $this->assertInstanceOf(PropertyMetadataInterface::class, $prop);
        $this->assertSame('scalar', $prop->getDataType());
        $this->assertSame('string', $prop->getDataTypeParams());

        $this->assertArrayHasKey('intProp', $properties);
        $prop = $properties['intProp'];
        $this->assertInstanceOf(PropertyMetadataInterface::class, $prop);
        $this->assertSame('scalar', $prop->getDataType());
        $this->assertSame('int', $prop->getDataTypeParams());

        $this->assertArrayHasKey('integerProp', $properties);
        $prop = $properties['integerProp'];
        $this->assertInstanceOf(PropertyMetadataInterface::class, $prop);
        $this->assertSame('scalar', $prop->getDataType());
        $this->assertSame('integer', $prop->getDataTypeParams());

        $this->assertArrayHasKey('boolProp', $properties);
        $prop = $properties['boolProp'];
        $this->assertInstanceOf(PropertyMetadataInterface::class, $prop);
        $this->assertSame('scalar', $prop->getDataType());
        $this->assertSame('bool', $prop->getDataTypeParams());

        $this->assertArrayHasKey('booleanProp', $properties);
        $prop = $properties['booleanProp'];
        $this->assertInstanceOf(PropertyMetadataInterface::class, $prop);
        $this->assertSame('scalar', $prop->getDataType());
        $this->assertSame('boolean', $prop->getDataTypeParams());

        $this->assertArrayHasKey('floatProp', $properties);
        $prop = $properties['floatProp'];
        $this->assertInstanceOf(PropertyMetadataInterface::class, $prop);
        $this->assertSame('scalar', $prop->getDataType());
        $this->assertSame('float', $prop->getDataTypeParams());

        $this->assertArrayHasKey('doubleProp', $properties);
        $prop = $properties['doubleProp'];
        $this->assertInstanceOf(PropertyMetadataInterface::class, $prop);
        $this->assertSame('scalar', $prop->getDataType());
        $this->assertSame('double', $prop->getDataTypeParams());

        $this->assertArrayHasKey('dateProp', $properties);
        $prop = $properties['dateProp'];
        $this->assertInstanceOf(PropertyMetadataInterface::class, $prop);
        $this->assertSame('datetime', $prop->getDataType());
        $this->assertNull($prop->getDataTypeParams());

        $this->assertArrayHasKey('timeProp', $properties);
        $prop = $properties['timeProp'];
        $this->assertInstanceOf(PropertyMetadataInterface::class, $prop);
        $this->assertSame('datetime', $prop->getDataType());
        $this->assertSame('H:i:s', $prop->getDataTypeParams());

        $this->assertArrayHasKey('datetimeProp', $properties);
        $prop = $properties['datetimeProp'];
        $this->assertInstanceOf(PropertyMetadataInterface::class, $prop);
        $this->assertSame('datetime', $prop->getDataType());
        $this->assertSame('Y-m-d H:i:s', $prop->getDataTypeParams());

        $this->assertArrayHasKey('rawArray', $properties);
        $prop = $properties['rawArray'];
        $this->assertInstanceOf(PropertyMetadataInterface::class, $prop);
        $this->assertSame('array', $prop->getDataType());
        $this->assertSame(['raw', null], $prop->getDataTypeParams());

        $this->assertArrayHasKey('strArray', $properties);
        $prop = $properties['strArray'];
        $this->assertInstanceOf(PropertyMetadataInterface::class, $prop);
        $this->assertSame('array', $prop->getDataType());
        $this->assertSame(['scalar', 'string'], $prop->getDataTypeParams());

        $this->assertArrayHasKey('dateArray', $properties);
        $prop = $properties['dateArray'];
        $this->assertInstanceOf(PropertyMetadataInterface::class, $prop);
        $this->assertSame('array', $prop->getDataType());
        $this->assertSame(['datetime', 'H:i:s'], $prop->getDataTypeParams());

        $this->assertArrayHasKey('intArray', $properties);
        $prop = $properties['intArray'];
        $this->assertInstanceOf(PropertyMetadataInterface::class, $prop);
        $this->assertSame('array', $prop->getDataType());
        $this->assertSame(['scalar', 'int'], $prop->getDataTypeParams());

        $this->assertArrayHasKey('rawProp', $properties);
        $prop = $properties['rawProp'];
        $this->assertInstanceOf(PropertyMetadataInterface::class, $prop);
        $this->assertSame('raw', $prop->getDataType());
        $this->assertNull($prop->getDataTypeParams());

        $this->assertArrayHasKey('objProp', $properties);
        $prop = $properties['objProp'];
        $this->assertInstanceOf(PropertyMetadataInterface::class, $prop);
        $this->assertSame('object', $prop->getDataType());
        $this->assertSame(AnotherObject::class, $prop->getDataTypeParams());
    }

    /**
     * @test
     */
    public function shouldLoadObjectDiscriminatorMetadata()
    {
        $metadata = $this->loader->loadClassMetadata(new \ReflectionClass(BaseObject::class));

        $this->assertInstanceOf(ObjectMetadataInterface::class, $metadata);
        $this->assertSame('parentProp', $metadata->getDiscriminatorField()->getPropertyName());
        $this->assertSame(ExampleObject::class, $metadata->getDiscriminatorClass('example'));
    }

    /**
     * @test
     */
    public function shouldLoadResourceMetadata()
    {
        $metadata = $this->loader->loadClassMetadata(new \ReflectionClass(Pet::class));

        $this->assertInstanceOf(ResourceMetadataInterface::class, $metadata);

        $attributes = $metadata->getAttributes();
        $this->assertInternalType('array', $attributes);

        $this->assertArrayHasKey('name', $attributes);
        $attr = $attributes['name'];
        $this->assertInstanceOf(PropertyMetadataInterface::class, $attr);
        $this->assertSame('scalar', $attr->getDataType());
        $this->assertSame('string', $attr->getDataTypeParams());
        $this->assertNull($attr->getSetter());

        $this->assertArrayHasKey('family', $attributes);
        $attr = $attributes['family'];
        $this->assertInstanceOf(PropertyMetadataInterface::class, $attr);
        $this->assertSame('scalar', $attr->getDataType());
        $this->assertSame('string', $attr->getDataTypeParams());
        $this->assertNull($attr->getSetter());

        $relationships = $metadata->getRelationships();
        $this->assertInternalType('array', $relationships);

        $this->assertArrayHasKey('store', $relationships);
        $rel = $relationships['store'];
        $this->assertInstanceOf(PropertyMetadataInterface::class, $rel);
        $this->assertSame('object', $rel->getDataType());
        $this->assertSame(Store::class, $rel->getDataTypeParams());
        $this->assertNull($rel->getSetter());
    }

    /**
     * @test
     */
    public function shouldLoadResourceDiscriminatorMetadata()
    {
        $metadata = $this->loader->loadClassMetadata(new \ReflectionClass(Pet::class));

        $this->assertInstanceOf(ResourceMetadataInterface::class, $metadata);
        $this->assertSame('family', $metadata->getDiscriminatorField()->getPropertyName());
        $this->assertSame(Cat::class, $metadata->getDiscriminatorClass('cats'));
        $this->assertSame(Dog::class, $metadata->getDiscriminatorClass('dogs'));
    }

    /**
     * @test
     */
    public function shouldLoadDocumentMetadata()
    {
        $metadata = $this->loader->loadClassMetadata(new \ReflectionClass(PetsList::class));

        $this->assertInstanceOf(DocumentMetadataInterface::class, $metadata);
        $this->assertSame(PetsList::class, $metadata->getClassName());
        $this->assertTrue($metadata->isAllowEmpty());

        $content = $metadata->getContentMetadata();
        $this->assertInstanceOf(PropertyMetadataInterface::class, $content);
        $this->assertSame('data', $content->getPropertyName());
        $this->assertSame('array', $content->getDataType());
        $this->assertSame(['object', Pet::class], $content->getDataTypeParams());
        $this->assertNull($content->getSetter());
    }

    /**
     * @test
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp #Couldn't find setter for non public property: .*#
     */
    public function shouldThrowIfThereAreNotSetterForNonPublicProperty()
    {
        $this->loader->loadClassMetadata(new \ReflectionClass(InvalidObject::class));

        $this->fail("Should throw exception if there are not settings for non public property");
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp #Custom parser function .* for property '.*' does not exist#
     */
    public function shouldThrowIfCustomParserFunctionIsNotDefined()
    {
        $this->loader->loadClassMetadata(new \ReflectionClass(InvalidObject2::class));

        $this->fail("Should throw exception if specified custom parser function is not defined");
    }
}
