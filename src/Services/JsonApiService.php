<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reva2\JsonApi\Services;

use InvalidArgumentException;
use Neomerx\JsonApi\Contracts\Http\Headers\HeaderParametersParserInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\MediaTypeInterface;
use Neomerx\JsonApi\Contracts\Schema\SchemaContainerInterface;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Neomerx\JsonApi\Schema\Error;
use Reva2\JsonApi\Contracts\Codec\CodecMatcherInterface;
use Reva2\JsonApi\Contracts\Decoders\DataParserInterface;
use Reva2\JsonApi\Contracts\Decoders\DecoderInterface;
use Reva2\JsonApi\Contracts\Encoder\EncodingParametersInterface;
use Reva2\JsonApi\Contracts\Factories\FactoryInterface;
use Reva2\JsonApi\Contracts\Http\Query\QueryParametersParserInterface;
use Reva2\JsonApi\Contracts\Http\RequestInterface;
use Reva2\JsonApi\Contracts\Services\EnvironmentInterface;
use Reva2\JsonApi\Contracts\Services\JsonApiRegistryInterface;
use Reva2\JsonApi\Contracts\Services\JsonApiServiceInterface;
use Reva2\JsonApi\Contracts\Services\ValidationServiceInterface;
use Reva2\JsonApi\Http\ResponseFactory;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Service for JSON API requests processing
 *
 * @package Reva2\JsonApi\Services
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class JsonApiService implements JsonApiServiceInterface
{
    /**
     * @var FactoryInterface
     */
    protected FactoryInterface $factory;

    /**
     * @var JsonApiRegistryInterface
     */
    protected JsonApiRegistryInterface $registry;

    /**
     * @var SchemaContainerInterface
     */
    protected SchemaContainerInterface $schemas;

    /**
     * @var ValidationServiceInterface
     */
    protected ValidationServiceInterface $validator;

    /**
     * @var DataParserInterface
     */
    protected DataParserInterface $parser;

    /**
     * Constructor
     *
     * @param FactoryInterface $factory
     * @param JsonApiRegistryInterface $registry
     * @param SchemaContainerInterface $schemas
     * @param DataParserInterface $parser
     * @param ValidationServiceInterface $validator
     */
    public function __construct(
        FactoryInterface $factory,
        JsonApiRegistryInterface $registry,
        SchemaContainerInterface $schemas,
        DataParserInterface $parser,
        ValidationServiceInterface $validator
    ) {
        $this->factory = $factory;
        $this->registry = $registry;
        $this->parser = $parser;
        $this->schemas = $schemas;
        $this->validator = $validator;
    }

    /**
     * @inheritdoc
     */
    public function getFactory(): FactoryInterface
    {
        return $this->factory;
    }

    /**
     * @inheritdoc
     */
    public function parseRequest(Request $request, EnvironmentInterface $environment = null): RequestInterface
    {
        if (null === $environment) {
            $environment = $this->getRequestEnvironment($request);
        }

        $this->initializeEnvironment($environment, $request);

        $prevGroups = $this->parser->getSerializationGroups();

        $this->parser->setSerializationGroups($environment->getSerializationGroups());

        $apiRequest = $this->factory->createRequest($environment);
        $apiRequest
            ->setQuery($this->parseQuery($request, $environment))
            ->setBody($this->parseBody($request, $environment));

        $this->parser->setSerializationGroups($prevGroups);

        if (null !== $environment->getValidationGroups()) {
            $this->validateRequest($apiRequest);
        }

        return $apiRequest;
    }

    /**
     * @inheritdoc
     */
    public function validateRequest(RequestInterface $request): void
    {
        $validationGroups = $request->getEnvironment()->getValidationGroups();
        if (is_bool($validationGroups)) {
            if (false === $validationGroups) {
                return;
            } else {
                $validationGroups = null;
            }
        }

        $errors = $this->validateData($request->getQuery(), $validationGroups);
        $errors = array_merge($errors, $this->validateData($request->getBody(), $validationGroups));

        if (0 === count($errors)) {
            return;
        }

        $code = null;
        foreach ($errors as $error) {
            /* @var $error Error */
            if (null === $code) {
                $code = $error->getStatus();
            } elseif ($code !== $error->getStatus()) {
                $code = 400;
                break;
            }
        }

        throw new JsonApiException($errors, $code);
    }

    /**
     * @inheritdoc
     */
    public function getResponseFactory(RequestInterface $request): ResponseFactory
    {
        return new ResponseFactory($this->schemas, $request->getEnvironment(), $request->getQuery());
    }

    /**
     * Returns JSON API environment configured in request
     *
     * @param Request $request
     * @return EnvironmentInterface
     */
    public function getRequestEnvironment(Request $request): EnvironmentInterface
    {
        if (false === $request->attributes->has('_jsonapi')) {
            throw new RuntimeException('JSON API environment is not provided');
        }

        $environment = $request->attributes->get('_jsonapi');
        if (!$environment instanceof EnvironmentInterface) {
            throw new InvalidArgumentException(sprintf(
                "JSON API environment should implement %s interface",
                EnvironmentInterface::class
            ));
        }

        return $environment;
    }

    /**
     * Initialize JSON API environment for specified request
     *
     * @param EnvironmentInterface $environment
     * @param Request $request
     * @return void
     */
    private function initializeEnvironment(EnvironmentInterface $environment, Request $request): void
    {
        $matcher = $this->createMatcher($environment);

        $this->parseRequestHeaders($request, $matcher);

        $environment
            ->setDecoder($matcher->getDecoder())
            ->setEncoder($matcher->getEncoder())
            ->setEncoderMediaType($matcher->getEncoderRegisteredMatchedType());
    }

    /**
     * Create codec matcher for specified environment
     *
     * @param EnvironmentInterface $environment
     * @return CodecMatcherInterface
     */
    private function createMatcher(EnvironmentInterface $environment): CodecMatcherInterface
    {
        $matcher = $this->factory->createCodecMatcher();

        $config = $environment->getMatcherConfiguration();
        if ((array_key_exists('decoders', $config)) && (is_array($config['decoders']))) {
            $this->registerDecoders($config['decoders'], $matcher);
        }

        if ((array_key_exists('encoders', $config)) && (is_array($config['encoders']))) {
            $this->registerEncoders($config['encoders'], $matcher);
        }

        return $matcher;
    }

    /**
     * Convert media type string to media type object
     *
     * @param string $type
     * @return MediaTypeInterface
     */
    private function parseMediaTypeString(string $type): MediaTypeInterface
    {
        $parts = explode('/', $type);
        if (2 !== count($parts)) {
            throw new InvalidArgumentException(sprintf("Invalid media type '%s' specified", $type));
        }

        return $this->factory->createMediaType($parts[0], $parts[1]);
    }

    /**
     * Parse request headers and detect appropriate decoder and encoder
     *
     * @param Request $request
     * @param CodecMatcherInterface $matcher
     */
    private function parseRequestHeaders(Request $request, CodecMatcherInterface $matcher): void
    {
        $parser = $this->factory->createHeaderParametersParser();

        $contentType = $parser->parseContentTypeHeader(
            $request->headers->get(HeaderParametersParserInterface::HEADER_ACCEPT)
        );

        $acceptContentTypes = $parser->parseAcceptHeader(
            $request->headers->get(HeaderParametersParserInterface::HEADER_ACCEPT)
        );

        $checker = $this->factory->createHeadersChecker($matcher);

        $checker->checkHeaders($contentType, $acceptContentTypes);
    }

    /**
     * Parse request query parameters
     *
     * @param Request $request
     * @param EnvironmentInterface $environment
     * @return EncodingParametersInterface|null
     */
    private function parseQuery(Request $request, EnvironmentInterface $environment): ?EncodingParametersInterface
    {
        if (null === $environment->getQueryType()) {
            return null;
        }

        $queryParser = $this->factory->createQueryParametersParser();
        if ($queryParser instanceof QueryParametersParserInterface) {
            $queryParser
                ->setDataParser($this->parser)
                ->setQueryType($environment->getQueryType());
        }

        return $queryParser->parse($request);
    }

    /**
     * Parse request body
     *
     * @param Request $request
     * @param EnvironmentInterface $environment
     * @return mixed|null
     */
    private function parseBody(Request $request, EnvironmentInterface $environment): mixed
    {
        if (null === $environment->getBodyType()) {
            return null;
        }

        $decoder = $environment->getDecoder();
        if ($decoder instanceof DecoderInterface) {
            $decoder->setContentType($environment->getBodyType());
        }

        return $decoder->decode($request->getContent());
    }

    /**
     * Validate specified data
     *
     * @param mixed $data
     * @param array|null $validationGroups
     * @return Error[]
     */
    private function validateData(mixed $data = null, array $validationGroups = null): array
    {
        return (null !== $data) ? $this->validator->validate($data, $validationGroups) : [];
    }

    /**
     * Register specified decoders
     *
     * @param array $decoders
     * @param CodecMatcherInterface $matcher
     */
    private function registerDecoders(array $decoders, CodecMatcherInterface $matcher): void
    {
        foreach ($decoders as $mediaType => $decoderType) {
            $matcher->registerDecoder(
                $this->parseMediaTypeString($mediaType),
                $this->registry->getDecoder($decoderType)
            );
        }
    }

    /**
     * Register specified encoders
     *
     * @param array $encoders
     * @param CodecMatcherInterface $matcher
     */
    private function registerEncoders(array $encoders, CodecMatcherInterface $matcher): void
    {
        foreach ($encoders as $mediaType => $encoderType) {
            $matcher->registerEncoder(
                $this->parseMediaTypeString($mediaType),
                $this->registry->getEncoder($encoderType)
            );
        }
    }
}
