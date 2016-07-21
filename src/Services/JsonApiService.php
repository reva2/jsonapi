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

use Neomerx\JsonApi\Contracts\Document\ErrorInterface;
use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Reva2\JsonApi\Contracts\Decoders\Data\DocumentInterface;
use Reva2\JsonApi\Contracts\Http\RequestInterface;
use Reva2\JsonApi\Contracts\Services\EnvironmentInterface;
use Reva2\JsonApi\Contracts\Services\JsonApiServiceInterface;
use Reva2\JsonApi\Contracts\Services\ValidationServiceInterface;
use Reva2\JsonApi\Http\ResponseFactory;
use Reva2\JsonApi\Http\Request as ApiRequest;
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
     * Current JSON API environment
     *
     * @var EnvironmentInterface
     */
    protected $environment;

    /**
     * Validation service
     *
     * @var ValidationServiceInterface
     */
    protected $validator;

    /**
     * Constructor
     *
     * @param ValidationServiceInterface $validator
     */
    public function __construct(ValidationServiceInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @inheritdoc
     */
    public function setEnvironment(EnvironmentInterface $environment)
    {
        $this->environment = $environment;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRequestMediaType()
    {
        return $this->getEnvironment()->getDecoderMediaType();
    }

    /**
     * @inheritdoc
     */
    public function getResponseMediaType()
    {
        return $this->getEnvironment()->getEncoderMediaType();
    }

    /**
     * @inheritdoc
     */
    public function parseRequest(Request $httpRequest, $validate = true)
    {
        $request = new ApiRequest(
            $this->parseQuery($httpRequest),
            $this->parseBody($httpRequest)
        );

        $validationGroups = null;
        if (is_array($validate)) {
            $validationGroups = $validate;
            $validate = true;
        } elseif (!is_bool($validate)) {
            throw new \InvalidArgumentException(
                'Parameter $validate must be a boolean or array containing validation groups'
            );
        }
        
        if (true === $validate) {
            $this->validateRequest($request, $validationGroups);
        }
        
        return $request;
    }

    /**
     * @inheritdoc
     */
    public function validateRequest(RequestInterface $request, array $groups = null)
    {
        $errors = $this->validator->validate($request->getQuery(), $groups);
        $errors = array_merge($errors, $this->validator->validate($request->getBody(), $groups));

        if (count($errors) > 0) {
            $code = null;

            foreach ($errors as $error) {
                /* @var $error ErrorInterface */

                if (null === $code) {
                    $code = $error->getStatus();
                } elseif ($code !== $error->getStatus()) {
                    $code = 400;
                    break;
                }
            }

            throw new JsonApiException($errors, $code);
        }
    }

    /**
     * @inheritdoc
     */
    public function getResponseFactory(EncodingParametersInterface $encodingParams)
    {
        return new ResponseFactory($this->getEnvironment(), $encodingParams);
    }

    /**
     * Parse query parameters
     *
     * @param Request $request
     * @return EncodingParametersInterface
     */
    private function parseQuery(Request $request)
    {
        return $this->getEnvironment()->getQueryParamsDecoder()->decode($request->query->all());
    }

    /**
     * Parse body parameters
     *
     * @param Request $request
     * @return DocumentInterface|null
     */
    private function parseBody(Request $request)
    {
        $body = $request->getContent();
        if (is_resource($body)) {
            $body = stream_get_contents($body);
        }

        return $this->environment->getDecoder()->decode($body);
    }

    /**
     * Returns environment for current request
     *
     * @return EnvironmentInterface
     */
    private function getEnvironment()
    {
        if (null === $this->environment) {
            throw new \RuntimeException('JSON API environment not specified');
        };

        return $this->environment;
    }
}
