<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) OrbitScripts LLC <support@orbitscripts.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Tests\Fixtures\Objects;

use Reva2\JsonApi\Annotations as API;

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
     * @API\Property(type="string")
     */
    public $strProp;

    /**
     * @var int
     * @API\Property(type="int")
     */
    public $intProp;

    /**
     * @var int
     * @API\Property(type="integer")
     */
    public $integerProp;

    /**
     * @var bool
     * @API\Property(type="bool")
     */
    public $boolProp;

    /**
     * @var bool
     * @API\Property(type="boolean")
     */
    public $booleanProp;

    /**
     * @var float
     * @API\Property(type="float")
     */
    public $floatProp;

    /**
     * @var double
     * @API\Property(type="double")
     */
    public $doubleProp;

    /**
     * @var \DateTimeImmutable
     * @API\Property(type="DateTime")
     */
    public $dateProp;

    /**
     * @var \DateTimeImmutable
     * @API\Property(type="DateTime<H:i:s>")
     */
    public $timeProp;

    /**
     * @var \DateTimeImmutable
     * @API\Property(type="DateTime<Y-m-d H:i:s>")
     */
    public $datetimeProp;

    /**
     * @var array
     * @API\Property(type="array")
     */
    public $rawArray;

    /**
     * @var string[]
     * @API\Property(type="Array<string>")
     */
    public $strArray;

    /**
     * @var int[]
     * @API\Property()
     */
    public $intArray;

    /**
     * @var \DateTimeImmutable[]
     * @API\Property(type="Array<DateTime<H:i:s>>")
     */
    public $dateArray;

    /**
     * @var AnotherObject[]
     * @API\Property(type="Array<Reva2\JsonApi\Tests\Fixtures\Objects\AnotherObject>")
     */
    public $objArray;

    /**
     * @var array
     * @API\Property(type="Array<Array<int>>")
     */
    public $arrArray;

    /**
     * @var string[]
     * @API\Property(parser="parseCustomProp")
     */
    public $customProp;

    /**
     * @API\Property()
     */
    public $rawProp;

    /**
     * @var AnotherObject
     * @API\Property(type="Reva2\JsonApi\Tests\Fixtures\Objects\AnotherObject")
     */
    public $objProp;

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