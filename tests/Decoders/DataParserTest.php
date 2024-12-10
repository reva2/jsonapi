<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Tests\Decoders;

use InvalidArgumentException;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Neomerx\JsonApi\Schema\Error;
use PHPUnit\Framework\TestCase;
use Reva2\JsonApi\Decoders\CallbackResolver;
use Reva2\JsonApi\Decoders\DataParser;
use Reva2\JsonApi\Decoders\Mapping\Factory\LazyMetadataFactory;
use Reva2\JsonApi\Decoders\Mapping\Loader\AttributeLoader;
use Reva2\JsonApi\Encoder\Parameters\SortParameter;
use Reva2\JsonApi\Http\Query\ListQueryParameters;
use Reva2\JsonApi\Tests\Fixtures\Documents\OfficesListDocument;
use Reva2\JsonApi\Tests\Fixtures\Documents\PetsListDocument;
use Reva2\JsonApi\Tests\Fixtures\Metadata\OfficesListMetadata;
use Reva2\JsonApi\Tests\Fixtures\Metadata\PetsListMetadata;
use Reva2\JsonApi\Tests\Fixtures\Objects\AnotherObject;
use Reva2\JsonApi\Tests\Fixtures\Objects\BaseObject;
use Reva2\JsonApi\Tests\Fixtures\Objects\ExampleObject;
use Reva2\JsonApi\Tests\Fixtures\Resources\Cat;
use Reva2\JsonApi\Tests\Fixtures\Resources\Dog;
use Reva2\JsonApi\Tests\Fixtures\Resources\Office;
use Reva2\JsonApi\Tests\Fixtures\Resources\Person;
use Reva2\JsonApi\Tests\Fixtures\Resources\Pet;
use Reva2\JsonApi\Tests\Fixtures\Resources\RussianDoll;
use Reva2\JsonApi\Tests\Fixtures\Resources\Something;
use Reva2\JsonApi\Tests\Fixtures\Resources\Store;
use Reva2\JsonApi\Tests\Fixtures\Resources\Table;
use Reva2\JsonApi\Tests\Fixtures\Resources\Window;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Test for data parser
 *
 * @package Reva2\JsonApi\Tests\Decoders
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class DataParserTest extends TestCase
{
    /**
     * @var DataParser
     */
    protected $parser;

    protected function setUp(): void
    {
        $loader = new AttributeLoader();
        $factory = new LazyMetadataFactory($loader);
        $resolver = new CallbackResolver();

        $this->parser = new DataParser($factory, $resolver);
    }

    /**
     * @test
     */
    public function shouldParseStrings()
    {
        $data = new \stdClass();
        $data->key = 'value';
        $data->invalid = new DateTime();

        $this->assertSame('value', $this->parser->parseString($data, 'key'));
        $this->assertNull($this->parser->parseString($data, 'unknown'));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('#Value expected to be a string, but .+ given#');

        $this->parser->parseString($data, 'invalid');
    }

    /**
     * @test
     */
    public function shouldParseIntegers()
    {
        $data = new \stdClass();
        $data->key = 10;
        $data->another = '10';
        $data->invalid = 'invalid';

        $this->assertSame(10, $this->parser->parseInt($data, 'key'));
        $this->assertSame(10, $this->parser->parseInt($data, 'another'));
        $this->assertNull($this->parser->parseInt($data, 'unknown'));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('#Value expected to be int, but .+ given#');

        $this->parser->parseInt($data, 'invalid');
    }

    /**
     * @test
     */
    public function shouldParseFloats()
    {
        $data = new \stdClass();
        $data->key = 10.5;
        $data->another = '10.5';
        $data->invalid = 'invalid';

        $this->assertSame(10.5, $this->parser->parseFloat($data, 'key'));
        $this->assertSame(10.5, $this->parser->parseFloat($data, 'another'));
        $this->assertNull($this->parser->parseFloat($data, 'unknown'));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('#alue expected to be float, but .+ given#');

        $this->parser->parseFloat($data, 'invalid');
    }

    /**
     * @test
     */
    public function shouldParseBooleans()
    {
        $data = new \stdClass();
        $data->bool = true;
        $data->yes = 'yes';
        $data->no = 'no';
        $data->trueString = 'true';
        $data->falseString = 'false';
        $data->y = 'y';
        $data->n = 'n';
        $data->on = 'on';
        $data->off = 'off';
        $data->enabled = 'enabled';
        $data->disabled = 'disabled';
        $data->number = 1;
        $data->zero = 0;
        $data->invalid = new \DateTime();


        $this->assertTrue($this->parser->parseBool($data, 'bool'));
        $this->assertTrue($this->parser->parseBool($data, 'yes'));
        $this->assertFalse($this->parser->parseBool($data, 'no'));
        $this->assertTrue($this->parser->parseBool($data, 'trueString'));
        $this->assertFalse($this->parser->parseBool($data, 'falseString'));
        $this->assertTrue($this->parser->parseBool($data, 'y'));
        $this->assertFalse($this->parser->parseBool($data, 'n'));
        $this->assertTrue($this->parser->parseBool($data, 'on'));
        $this->assertFalse($this->parser->parseBool($data, 'off'));
        $this->assertTrue($this->parser->parseBool($data, 'enabled'));
        $this->assertFalse($this->parser->parseBool($data, 'disabled'));
        $this->assertTrue($this->parser->parseBool($data, 'number'));
        $this->assertFalse($this->parser->parseBool($data, 'zero'));
        $this->assertNull($this->parser->parseBool($data, 'unknown'));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('#Value expected to be a boolean, but .+ given#');

        $this->parser->parseBool($data, 'invalid');
    }

    /**
     * @test
     */
    public function shouldParseDateTimes()
    {
        $data = new \stdClass();
        $data->date = '2016-07-01';
        $data->datetime = '2016-07-01 18:29:15';
        $data->time = '18:29:15';
        $data->invalid = 'invalid';

        $date = $this->parser->parseDateTime($data, 'date');
        $this->assertInstanceOf(\DateTimeImmutable::class, $date);
        $this->assertEquals($data->date, $date->format('Y-m-d'));

        $datetime = $this->parser->parseDateTime($data, 'datetime', 'Y-m-d H:i:s');
        $this->assertInstanceOf(\DateTimeImmutable::class, $datetime);
        $this->assertEquals($data->datetime, $datetime->format('Y-m-d H:i:s'));

        $time = $this->parser->parseDateTime($data, 'time', 'H:i:s');
        $this->assertInstanceOf(\DateTimeImmutable::class, $time);
        $this->assertEquals($data->time, $time->format('H:i:s'));

        $this->assertNull($this->parser->parseDateTime($data, 'unknown'));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value expected to be a date/time string in \'Y-m-d\' format');

        $this->parser->parseDateTime($data, 'invalid');
    }

    /**
     * @test
     */
    public function shouldParseArrays()
    {
        $data = new \stdClass();
        $data->arrayValue = ['first', 'second', 'third'];
        $data->invalid = false;

        $itemParser = function ($data, $path, DataParser $parser) {
            $value = $parser->parseString($data, $path);

            return $value[0];
        };

        $this->assertSame(['f', 's', 't'], $this->parser->parseArray($data, 'arrayValue', $itemParser));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('#Value expected to be an array, but .+ given#');

        $this->parser->parseArray($data, 'invalid', $itemParser);
    }

    /**
     * @test
     */
    public function shouldParseObject()
    {
        $data = $this->getDataFromFile('object.json');

        $val = $this->parser->parseObject($data, 'test', BaseObject::class);

        $this->assertInstanceOf(ExampleObject::class, $val);
        /* @var $val ExampleObject */

        $this->assertSame('example', $val->getParentProp());
        $this->assertSame('test string', $val->strProp);
        $this->assertSame(10, $val->intProp);
        $this->assertSame(15, $val->integerProp);
        $this->assertTrue($val->boolProp);
        $this->assertFalse($val->booleanProp);
        $this->assertSame(15.5, $val->floatProp);
        $this->assertSame(10.5, $val->doubleProp);

        $this->assertInstanceOf(\DateTimeImmutable::class, $val->datetimeProp);
        $this->assertSame('2016-07-21', $val->dateProp->format('Y-m-d'));

        $this->assertInstanceOf(\DateTimeImmutable::class, $val->timeProp);
        $this->assertSame('12:00:00', $val->timeProp->format('H:i:s'));

        $this->assertInstanceOf(\DateTimeImmutable::class, $val->datetimeProp);
        $this->assertSame('2016-07-21 12:00:00', $val->datetimeProp->format('Y-m-d H:i:s'));

        $this->assertSame([1, 'test', true], $val->rawArray);
        $this->assertSame(['first', 'second', 'third'], $val->strArray);
        $this->assertSame([1, 2, 3], $val->intArray);

        $this->assertIsArray($val->dateArray);
        $this->assertCount(2, $val->dateArray);
        $this->assertInstanceOf(\DateTimeImmutable::class, $val->dateArray[0]);
        $this->assertSame('12:00:00', $val->dateArray[0]->format('H:i:s'));
        $this->assertInstanceOf(\DateTimeImmutable::class, $val->dateArray[1]);
        $this->assertSame('12:30:00', $val->dateArray[1]->format('H:i:s'));

        $this->assertIsArray($val->objArray);
        $this->assertCount(2, $val->objArray);
        $this->assertInstanceOf(AnotherObject::class, $val->objArray[0]);
        $this->assertSame('another1', $val->objArray[0]->name);
        $this->assertInstanceOf(AnotherObject::class, $val->objArray[1]);
        $this->assertSame('another2', $val->objArray[1]->name);

        $this->assertSame([[1, 2], [3, 4]], $val->arrArray);

        $this->assertSame(['first', 'second', 'third'], $val->customProp);

        $this->assertInstanceOf(\stdClass::class, $val->rawProp);

        $this->assertInstanceOf(AnotherObject::class, $val->objProp);
        $this->assertSame('another', $val->objProp->name);
    }

    /**
     * @test
     */
    public function shouldThrowIfObjectDiscriminatorFieldEmpty()
    {
        $data = $this->getDataFromFile('resource.json');
        unset($data->pet->attributes->family);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Field value required and can not be empty');
        $this->expectExceptionCode(422);

        $this->parser->parseResource($data, 'pet', Pet::class);
    }

    /**
     * @test
     */
    public function shouldParseResource()
    {
        $data = $this->getDataFromFile('resource.json');

        $result = $this->parser->parseResource($data, 'pet', Pet::class);

        $this->assertInstanceOf(Cat::class, $result);
        /* @var $result Cat */

        $this->assertSame('mycat', $result->id);
        $this->assertSame('Kitty', $result->name);
        $this->assertSame('virtual', $result->getVirtualAttr());

        $this->assertInstanceOf(Store::class, $result->store);
        $this->assertSame('mystore', $result->store->getId());
        $this->assertTrue($result->store->isConverted());

        $this->assertInstanceOf(Something::class, $result->getVirtualRel());
        $this->assertSame('test', $result->getVirtualRel()->id);
    }

    /**
     * @test
     */
    public function shouldParseHierarchicalListResource()
    {
        $data = $this->getDataFromFile('russian-doll-response.json');

        $itemParser = function ($data, $path, DataParser $parser) {
            $values = $parser->parseResource($data, $path, RussianDoll::class);

            return $values;
        };

        $dolls = $this->parser->parseArray($data, 'data', $itemParser);

        /** @var RussianDoll $doll */
        foreach ($dolls as $doll) {
            $this->assertInstanceOf(RussianDoll::class, $doll);
            if ('large' === $doll->id) {
                $this->assertNull($doll->containedBy);
            } elseif ('medium' === $doll->id) {
                $this->assertSame('large', $doll->containedBy->id);
            } else {
                $this->assertSame('medium', $doll->containedBy->id);
            }
        }
    }

    /**
     * @test
     */
    public function shouldParseDocuments()
    {
        $data = $this->getDataFromFile('document.json');

        $doc = $this->parser->parseDocument($data, PetsListDocument::class);

        $this->assertInstanceOf(PetsListDocument::class, $doc);
        /* @var $doc PetsListDocument */

        $this->assertIsArray($doc->data);
        $this->assertCount(2, $doc->data);

        $cat = $doc->data[0];
        $this->assertInstanceOf(Cat::class, $cat);
        $this->assertEquals($data->data[0]->id, $cat->id);
        $this->assertEquals($data->data[0]->attributes->name, $cat->name);
        $this->assertEquals($data->data[0]->attributes->family, $cat->family);

        $store = $cat->store;
        $this->assertInstanceOf(Store::class, $store);
        $this->assertEquals($data->included[0]->id, $store->getId());
        $this->assertEquals($data->included[0]->attributes->name, $store->getName());
        $this->assertEquals($data->included[0]->attributes->address, $store->getAddress());

        $this->assertIsArray($cat->owners);
        $this->assertCount(2, $cat->owners);

        $mom = $cat->owners[0];
        $this->assertInstanceOf(Person::class, $mom);
        $this->assertEquals($data->included[1]->id, $mom->id);
        $this->assertEquals($data->included[1]->attributes->firstName, $mom->firstName);
        $this->assertEquals($data->included[1]->attributes->lastName, $mom->lastName);

        $dad = $cat->owners[1];
        $this->assertInstanceOf(Person::class, $dad);
        $this->assertEquals($data->included[2]->id, $dad->id);
        $this->assertEquals($data->included[2]->attributes->firstName, $dad->firstName);
        $this->assertEquals($data->included[2]->attributes->lastName, $dad->lastName);

        $dog = $doc->data[1];
        $this->assertInstanceOf(Dog::class, $dog);
        $this->assertEquals($data->data[1]->id, $dog->id);
        $this->assertEquals($data->data[1]->attributes->name, $dog->name);
        $this->assertEquals($data->data[1]->attributes->family, $dog->family);
        $this->assertSame($store, $dog->store);
        $this->assertIsArray($dog->owners);
        $this->assertCount(2, $dog->owners);
        $this->assertSame($mom, $dog->owners[0]);
        $this->assertSame($dad, $dog->owners[1]);

        $this->assertInstanceOf(PetsListMetadata::class, $doc->meta);
        $this->assertSame('test', $doc->meta->someString);
        $this->assertSame(1, $doc->meta->someInt);
    }

    /**
     * @test
     */
    public function shouldParseRelationshipToParent()
    {
        $data = $this->getDataFromFile('document-relationship-to-parent.json');

        try {
            $doc = $this->parser->parseDocument($data, OfficesListDocument::class);
            /** @var OfficesListDocument $doc */

            $this->assertInstanceOf(OfficesListDocument::class, $doc);

            $this->assertCount(1, $doc->data);

            $office = $doc->data[0];
            $this->assertInstanceOf(Office::class, $office);

            $table = $office->table;
            $this->assertInstanceOf(Table::class, $table);
            $this->assertEquals($data->included[0]->id, $table->id);
            $this->assertEquals($data->included[0]->attributes->name, $table->name);
            $this->assertEquals($data->included[0]->attributes->height, $table->height);
            $this->assertSame($office, $table->office);

            $this->assertCount(2, $office->windows);

            $windowOne = $office->windows[0];
            $this->assertInstanceOf(Window::class, $windowOne);
            $this->assertEquals($data->included[1]->id, $windowOne->id);
            $this->assertEquals($data->included[1]->attributes->name, $windowOne->name);
            $this->assertEquals($data->included[1]->attributes->layers, $windowOne->layers);
            $this->assertSame($office, $windowOne->office);

            $windowTwo = $office->windows[1];
            $this->assertInstanceOf(Window::class, $windowTwo);
            $this->assertEquals($data->included[2]->id, $windowTwo->id);
            $this->assertEquals($data->included[2]->attributes->name, $windowTwo->name);
            $this->assertEquals($data->included[2]->attributes->layers, $windowTwo->layers);
            $this->assertSame($office, $windowTwo->office);

            $this->assertInstanceOf(OfficesListMetadata::class, $doc->meta);
            $this->assertSame(12, $doc->meta->someInt);
            $this->assertSame(1, $doc->meta->someInnerIntOne);
            $this->assertSame(2, $doc->meta->someInnerIntTwo);
        } catch (JsonApiException $e) {
            $this->fail('It must parse this document without exceptions');
        }
    }

    /**
     * @test
     */
    public function shouldParseDocumentWithErrors()
    {
        $data = $this->getDataFromFile('error-document.json');

        try {
            $this->parser->parseDocument($data, PetsListDocument::class);

            $this->fail('It must throw exception on document with error');
        } catch (JsonApiException $e) {
            $errors = $e->getErrors();

            $this->assertCount(2, $errors);

            $this->assertEquals($data->errors[0]->id, $errors[0]->getId());
            $this->assertEquals($data->errors[0]->status, $errors[0]->getStatus());
            $this->assertEquals($data->errors[0]->code, $errors[0]->getCode());
            $this->assertEquals($data->errors[0]->title, $errors[0]->getTitle());
            $this->assertEquals($data->errors[0]->detail, $errors[0]->getDetail());
            $this->assertEquals(['pointer' => $data->errors[0]->source->pointer], $errors[0]->getSource());
            $this->assertSame($data->errors[0]->meta, $errors[0]->getMeta());

            $this->assertEquals($data->errors[1]->id, $errors[1]->getId());
            $this->assertEquals($data->errors[1]->status, $errors[1]->getStatus());
            $this->assertEquals($data->errors[1]->code, $errors[1]->getCode());
            $this->assertEquals($data->errors[1]->title, $errors[1]->getTitle());
            $this->assertEquals($data->errors[1]->detail, $errors[1]->getDetail());
            $this->assertEquals(['parameter' => $data->errors[1]->source->parameter], $errors[1]->getSource());
        }
    }



    /**
     * @test
     */
    public function shouldThrowOnInvalidDocumentType()
    {
        $data = $this->getDataFromFile('document.json');

        try {
            $this->parser->parseDocument($data, Pet::class);

            $this->fail('Should throw exception on invalid document type');
        } catch (JsonApiException $e) {
            $this->assertSame(500, $e->getHttpCode());

            $errors = $e->getErrors();
            /* @var $errors Error[] */
            $this->assertRegExp('~Failed to parse .* as JSON API document~si', $errors[0]->getDetail());
        }
    }

    /**
     * @test
     */
    public function shouldThrowJsonApiExceptionOnInvalidDocument()
    {
        try {
            $data = $this->getDataFromFile('invalid-document.json');
            $res = $this->parser->parseDocument($data, PetsListDocument::class);

            $this->fail("Should throw exception on invalid document");
        } catch (JsonApiException $e) {
            $this->assertSame(409, $e->getHttpCode());

            $error = $e->getErrors()[0];
            $this->assertInstanceOf(Error::class, $error);
            /* @var $error Error */

            $this->assertEquals(409, $error->getStatus());
            $this->assertSame(DataParser::ERROR_CODE, $error->getCode());
            $this->assertSame("Value must contain resource of type 'stores'", $error->getDetail());
            $this->assertSame(['pointer' => '/data/0/relationships/store/data'], $error->getSource());
        }
    }

    /**
     * @test
     */
    public function shouldParseQueryParams()
    {
        $data = [
            'include' => 'store,store.owner',
            'fields' => [
                'pets' => 'name,family',
                'stores' => 'name'
            ],
            'page' => [
                'number' => '2',
                'size' => '15'
            ],
            'sort' => '-store.id,name'
        ];

        $query = $this->parser->parseQueryParams($data, ListQueryParameters::class);

        $this->assertInstanceOf(ListQueryParameters::class, $query);
        $this->assertSame(['store', 'store.owner'], $query->getIncludePaths());
        $this->assertSame(['name', 'family'], $query->getFieldSet('pets'));
        $this->assertSame(['name'], $query->getFieldSet('stores'));
        $this->assertSame(['number' => 2, 'size' => 15], $query->getPaginationParameters());

        $sort = $query->getSortParameters();
        $this->assertIsArray($sort);
        $this->assertCount(2, $sort);

        $this->assertInstanceOf(SortParameter::class, $sort[0]);
        $this->assertSame('store.id', $sort[0]->getField());
        $this->assertFalse($sort[0]->isAscending());

        $this->assertInstanceOf(SortParameter::class, $sort[1]);
        $this->assertSame('name', $sort[1]->getField());
        $this->assertTrue($sort[1]->isAscending());
    }

    /**
     * Return data from specified json file
     *
     * @param string $filename
     * @return mixed
     */
    private function getDataFromFile($filename)
    {
        $path = __DIR__ . '/../Fixtures/Data/' . $filename;

        return json_decode(file_get_contents($path));
    }
}
