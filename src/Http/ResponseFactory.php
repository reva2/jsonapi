<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Http;

use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;
use Neomerx\JsonApi\Contracts\Schema\ContainerInterface;
use Neomerx\JsonApi\Http\Responses;
use Reva2\JsonApi\Contracts\Services\EnvironmentInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * JSON API response factory
 *
 * @package Reva2\JsonApi\Http
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class ResponseFactory extends Responses
{
    /**
     * @var ContainerInterface
     */
    protected $schemas;

    /**
     * @var EnvironmentInterface
     */
    protected $environment;

    /**
     * @var EncodingParametersInterface
     */
    protected $params;

    /**
     * Constructor
     *
     * @param ContainerInterface $schemas
     * @param EnvironmentInterface $environment
     * @param EncodingParametersInterface|null $params
     */
    public function __construct(
        ContainerInterface $schemas,
        EnvironmentInterface $environment,
        EncodingParametersInterface $params = null
    ) {
        $this->schemas = $schemas;
        $this->environment = $environment;
        $this->params = $params;
    }

    /**
     * @inheritdoc
     */
    protected function createResponse($content, $statusCode, array $headers)
    {
        return new Response($content, $statusCode, $headers);
    }

    /**
     * @inheritdoc
     */
    protected function getEncoder()
    {
        return $this->environment->getEncoder();
    }

    /**
     * @inheritdoc
     */
    protected function getUrlPrefix()
    {
        return $this->environment->getUrlPrefix();
    }

    /**
     * @inheritdoc
     */
    protected function getEncodingParameters()
    {
        return $this->params;
    }

    /**
     * @inheritdoc
     */
    protected function getSchemaContainer()
    {
        return $this->schemas;
    }

    /**
     * @inheritdoc
     */
    protected function getSupportedExtensions()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    protected function getMediaType()
    {
        return $this->environment->getEncoderMediaType();
    }
}
