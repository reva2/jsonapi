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
use Reva2\JsonApi\Contracts\Decoders\Mapping\ObjectMetadataInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\PropertyMetadataInterface;
use Reva2\JsonApi\Decoders\Mapping\Loader\AnnotationLoader;
use Reva2\JsonApi\Tests\Fixtures\Objects\AnotherObject;
use Reva2\JsonApi\Tests\Fixtures\Objects\ExampleObject;

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

        $this->assertArrayHasKey('parentProp', $properties);
        $prop = $properties['parentProp'];
        $this->assertInstanceOf(PropertyMetadataInterface::class, $prop);
        $this->assertSame('scalar', $prop->getDataType());
        $this->assertSame('string', $prop->getDataTypeParams());
        $this->assertSame('setParentProp', $prop->getSetter());
    }
}
