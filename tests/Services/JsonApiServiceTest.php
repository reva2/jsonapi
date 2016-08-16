<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) OrbitScripts LLC <support@orbitscripts.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Tests\Services;

use Doctrine\Common\Annotations\AnnotationReader;
use Neomerx\JsonApi\Contracts\Http\ResponsesInterface;
use Neomerx\JsonApi\Contracts\Schema\ContainerInterface;
use Reva2\JsonApi\Contracts\Decoders\DataParserInterface;
use Reva2\JsonApi\Contracts\Factories\FactoryInterface;
use Reva2\JsonApi\Contracts\Http\RequestInterface;
use Reva2\JsonApi\Decoders\DataParser;
use Reva2\JsonApi\Decoders\Mapping\Factory\LazyMetadataFactory;
use Reva2\JsonApi\Decoders\Mapping\Loader\AnnotationLoader;
use Reva2\JsonApi\Decoders\RequestDecoder;
use Reva2\JsonApi\Encoder\Encoder;
use Reva2\JsonApi\Factories\Factory;
use Reva2\JsonApi\Services\Environment;
use Reva2\JsonApi\Services\JsonApiRegistry;
use Reva2\JsonApi\Services\JsonApiService;
use Reva2\JsonApi\Services\ValidationService;
use Reva2\JsonApi\Tests\Fixtures\Documents\PetDocument;
use Reva2\JsonApi\Tests\Fixtures\Query\PetQuery;
use Reva2\JsonApi\Tests\Fixtures\Query\PetsListQuery;
use Reva2\JsonApi\Tests\Fixtures\Resources\Cat;
use Reva2\JsonApi\Tests\Fixtures\Resources\Dog;
use Reva2\JsonApi\Tests\Fixtures\Resources\Pet;
use Reva2\JsonApi\Tests\Fixtures\Resources\Store;
use Reva2\JsonApi\Tests\Fixtures\Schemas\PetSchema;
use Reva2\JsonApi\Tests\Fixtures\Schemas\StoreSchema;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validation;

/**
 * Tests for JSON API service
 *
 * @package Reva2\JsonApi\Tests\Services
 * @author Sergey Revenko <reva2@orbita1.ru>
 */
class JsonApiServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DataParserInterface
     */
    protected $parser;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var JsonApiService
     */
    protected $service;

    /**
     * @test
     */
    public function shouldParseRequest()
    {
        $request = Request::create(
            'http://test.com/api/pets/my-cat',
            Request::METHOD_PATCH,
            [],
            [],
            [],
            [],
            $this->getDataFromFile('update-pet-request.json')
        );

        $request->headers->add([
            'Content-Type' => 'application/vnd.json+api',
            'Accept' => 'application/vnd.json+api'
        ]);

        $request->query->set('include', 'store');

        $environment = new Environment();
        $environment
            ->setQueryType(PetQuery::class)
            ->setBodyType(PetDocument::class)
            ->setMatcherConfiguration([
                'decoders' => [
                    'application/vnd.json+api' => 'jsonapi.decoders.jsonapi'
                ],
                'encoders' => [
                    'application/vnd.json+api' => 'jsonapi.encoders.jsonapi'
                ]
            ])
            ->setValidationGroups(['Default']);

        $request->attributes->set('_jsonapi', $environment);

        $apiRequest = $this->service->parseRequest($request);

        $this->assertInstanceOf(RequestInterface::class, $apiRequest);

        $query = $apiRequest->getQuery();
        $this->assertInstanceOf(PetQuery::class, $query);
        /* @var $query PetQuery */

        $this->assertSame(['store'], $query->getIncludePaths());
        $this->assertNull($query->getFieldSets());

        $body = $apiRequest->getBody();
        $this->assertInstanceOf(PetDocument::class, $body);
        /* @var $body PetDocument */

        $pet = $body->data;
        $this->assertInstanceOf(Cat::class, $pet);
        /* @var $pet Cat */

        $this->assertSame('11', $pet->id);
        $this->assertSame('cats', $pet->family);
        $this->assertSame('Kitty', $pet->name);

        $store = $pet->store;
        $this->assertInstanceOf(Store::class, $store);
        /* @var $store Store */

        $this->assertSame('my-store', $store->getId());
    }

    /**
     * @test
     */
    public function shouldParseRequestWithoutParamsAndBody()
    {
        $request = Request::create('http://test.com/api/pets/my-cat', Request::METHOD_GET);
        $request->headers->add([
            'Content-Type' => 'application/vnd.json+api',
            'Accept' => 'application/vnd.json+api'
        ]);

        $environment = new Environment();
        $environment
            ->setMatcherConfiguration([
                'decoders' => [
                    'application/vnd.json+api' => 'jsonapi.decoders.jsonapi'
                ],
                'encoders' => [
                    'application/vnd.json+api' => 'jsonapi.encoders.jsonapi'
                ]
            ])
            ->setValidationGroups(['Default']);

        $request->attributes->set('_jsonapi', $environment);

        $apiRequest = $this->service->parseRequest($request);
        $this->assertInstanceOf(RequestInterface::class, $apiRequest);
        /* @var $apiRequest RequestInterface */

        $this->assertNull($apiRequest->getQuery());
        $this->assertNull($apiRequest->getBody());
    }

    /**
     * @test
     */
    public function shouldReturnAppropriateResponseFactory()
    {
        $request = Request::create('http://test.com/api/pets/my-cat', Request::METHOD_GET);
        $request->headers->add([
            'Content-Type' => 'application/vnd.json+api',
            'Accept' => 'application/vnd.json+api'
        ]);

        $request->query->set('include', 'store');
        $request->query->set('fields', ['pets' => 'family,store', 'stores' => 'name']);

        $environment = new Environment();
        $environment
            ->setQueryType(PetsListQuery::class)
            ->setMatcherConfiguration([
                'decoders' => [
                    'application/vnd.json+api' => 'jsonapi.decoders.jsonapi'
                ],
                'encoders' => [
                    'application/vnd.json+api' => 'jsonapi.encoders.jsonapi'
                ]
            ])
            ->setUrlPrefix('http://my-api.org');

        $request->attributes->set('_jsonapi', $environment);

        $apiRequest = $this->service->parseRequest($request);

        $store = new Store();
        $store
            ->setId('my-store')
            ->setName('My store')
            ->setAddress('Unknown street');

        $cat = new Cat();
        $cat->id = 'my-cat';
        $cat->family = 'cats';
        $cat->name = 'Kitty';
        $cat->store = $store;

        $dog = new Dog();
        $dog->id = 'my-dog';
        $dog->family = 'dogs';
        $dog->name = 'Sharik';
        $dog->store = $store;

        $responseFactory = $this->service->getResponseFactory($apiRequest);
        /* @var $responseFactory ResponsesInterface */

        $response = $responseFactory->getContentResponse([$cat, $dog]);
        $this->assertInstanceOf(Response::class, $response);
        /* @var $response Response */

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/vnd.json+api', $response->headers->get('Content-Type'));

        $expected = json_decode($this->getDataFromFile('pets-list-response.json'), true);
        $actual = json_decode($response->getContent(), true);

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @expectedException \RuntimeException
     * @expectedExceptionMessage JSON API environment is not provided
     */
    public function shouldThrowIfEnvironmentNotSpecified()
    {
        $request = Request::create('http://test.com/api/pets/my-cat', Request::METHOD_GET);
        $request->headers->add([
            'Content-Type' => 'application/vnd.json+api',
            'Accept' => 'application/vnd.json+api'
        ]);

        $this->service->parseRequest($request);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp #JSON API environment should implement .+ interface#
     */
    public function shouldThrowOnInvalidEnvironment()
    {
        $request = Request::create('http://test.com/api/pets/my-cat', Request::METHOD_GET);
        $request->headers->add([
            'Content-Type' => 'application/vnd.json+api',
            'Accept' => 'application/vnd.json+api'
        ]);

        $request->attributes->set('_jsonapi', []);

        $this->service->parseRequest($request);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessageRegExp  #Invalid media type .+ specified#
     */
    public function shouldThrowOnInvalidMediaTypes()
    {
        $request = Request::create('http://test.com/api/pets/my-cat', Request::METHOD_GET);
        $request->headers->add([
            'Content-Type' => 'application/vnd.json+api',
            'Accept' => 'application/vnd.json+api'
        ]);

        $environment = new Environment();
        $environment
            ->setMatcherConfiguration([
                'decoders' => [
                    'vnd.json+api' => 'jsonapi.decoders.jsonapi'
                ],
                'encoders' => [
                    'vnd.json+api' => 'jsonapi.encoders.jsonapi'
                ]
            ])
            ->setValidationGroups(['Default']);

        $request->attributes->set('_jsonapi', $environment);

        $this->service->parseRequest($request);
    }

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $factory = $this->getApiFactory();

        $this->service = new JsonApiService(
            $factory,
            $this->getRegistry($factory),
            $this->getContainer($factory),
            $this->getDataParser(),
            $this->getValidator()
        );
    }

    private function getApiFactory()
    {
        return new Factory();
    }

    /**
     * Returns configured decoders/encoders factory
     *
     * @param FactoryInterface $factory
     * @return JsonApiRegistry
     */
    private function getRegistry(FactoryInterface $factory)
    {
        $registry = new JsonApiRegistry();

        $decoder = $this->getDecoder();
        $registry->registerDecoder(
            'jsonapi.decoders.jsonapi',
            function () use ($decoder) {
                return $decoder;
            }
        );

        $encoder = $this->getEncoder($factory);
        $registry->registerEncoder(
            'jsonapi.encoders.jsonapi',
            function () use ($encoder) {
                return $encoder;
            }
        );

        return $registry;
    }

    /**
     * Returns JSON API request decoder
     *
     * @return RequestDecoder
     */
    private function getDecoder()
    {
        return new RequestDecoder($this->getDataParser());
    }

    /**
     * Returns JSON API data parser
     *
     * @return DataParser
     */
    private function getDataParser()
    {
        if (null === $this->parser) {
            $this->parser = new DataParser($this->getMetadataFactory());
        }

        return $this->parser;
    }

    /**
     * Returns JSON API metadata factory
     *
     * @return LazyMetadataFactory
     */
    private function getMetadataFactory()
    {
        return new LazyMetadataFactory(new AnnotationLoader(new AnnotationReader()));
    }

    /**
     * Returns JSON API response encoder
     *
     * @param FactoryInterface $factory
     * @return Encoder
     */
    private function getEncoder(FactoryInterface $factory)
    {
        return new Encoder($factory, $this->getContainer($factory));
    }

    /**
     * Returns resource schemas
     *
     * @param FactoryInterface $factory
     * @return array
     */
    private function getSchemas(FactoryInterface $factory)
    {
        $petClosure = function () use ($factory) {
            return new PetSchema($factory);
        };

        return [
            Pet::class => $petClosure,
            Cat::class => $petClosure,
            Dog::class => $petClosure,
            Store::class => function () use ($factory) { return new StoreSchema($factory); }
        ];
    }

    /**
     * Returns schemas container
     *
     * @param FactoryInterface $factory
     * @return ContainerInterface
     */
    private function getContainer(FactoryInterface $factory)
    {
        if (null === $this->container) {
            $this->container = $factory->createContainer($this->getSchemas($factory));
        }

        return $this->container;
    }

    /**
     * Returns configured validation service
     *
     * @return ValidationService
     */
    private function getValidator()
    {
        return new ValidationService(Validation::createValidator());
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

        return file_get_contents($path);
    }
}
