<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Tests\Fixtures\Objects;

use Reva2\JsonApi\Attributes as API;

/**
 * Example object
 *
 * @package Reva2\JsonApi\Tests\Fixtures\Objects
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class ExampleObject extends BaseObject
{
    /**
     * @var string
     */
    #[API\Property(type: "string")]
    public string $strProp;

    /**
     * @var int
     */
    #[API\Property(type: "int")]
    public int $intProp;

    /**
     * @var int
     */
    #[API\Property(type: "integer")]
    public $integerProp;

    /**
     * @var bool
     */
    #[API\Property(type: "bool")]
    public bool $boolProp;

    /**
     * @var bool
     */
    #[API\Property(type: "boolean")]
    public bool $booleanProp;

    /**
     * @var float
     */
    #[API\Property(type: "float")]
    public float $floatProp;

    /**
     * @var double
     */
    #[API\Property(type: "double")]
    public float $doubleProp;

    /**
     * @var \DateTimeImmutable
     */
    #[API\Property(type: "DateTime")]
    public \DateTimeImmutable $dateProp;

    /**
     * @var \DateTimeImmutable
     */
    #[API\Property(type: "DateTime<H:i:s>")]
    public \DateTimeImmutable $timeProp;

    /**
     * @var \DateTimeImmutable
     */
    #[API\Property(type: "DateTime<Y-m-d H:i:s>")]
    public \DateTimeImmutable $datetimeProp;

    /**
     * @var array
     */
    #[API\Property(type: "array")]
    public array $rawArray;

    /**
     * @var string[]
     */
    #[API\Property(type: "Array<string>")]
    public array $strArray;

    /**
     * @var int[]
     * @API\Property()
     */
    #[API\Property()]
    public array $intArray;

    /**
     * @var \DateTimeImmutable[]
     */
    #[API\Property(type: "Array<DateTime<H:i:s>>")]
    public array $dateArray;

    /**
     * @var AnotherObject[]
     */
    #[API\Property(type: "Array<Reva2\JsonApi\Tests\Fixtures\Objects\AnotherObject>")]
    public array $objArray;

    /**
     * @var array
     */
    #[API\Property(type: "Array<Array<int>>")]
    public array $arrArray;

    /**
     * @var string[]
     */
    #[API\Property(parser: "parseCustomProp")]
    public array $customProp;

    /**
     * @API\Property()
     */
    #[API\Property]
    public $rawProp;

    /**
     * @var AnotherObject
     */
    #[API\Property(type: AnotherObject::class)]
    public AnotherObject $objProp;

    /**
     * @var int
     */
    #[API\Property(type: "raw")]
    public int $rawPropWithDockblock;

    /**
     * Sets virtual property value
     *
     * @param mixed $value
     */
    #[API\VirtualProperty(name: "virtual", type: "string")]
    public function setVirtualProperty($value)
    {
        // Nothing to do here
    }

    public function getVirtualProperty()
    {
        return 'virtual';
    }

    /**
     * Parse value of customProp field
     *
     * @param mixed $value
     * @return array
     */
    public function parseCustomProp($value) {
        return explode(', ', (string) $value);
    }
}