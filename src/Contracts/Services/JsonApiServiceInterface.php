<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reva2\JsonApi\Contracts\Services;

use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\MediaTypeInterface;
use Neomerx\JsonApi\Contracts\Http\ResponsesInterface;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Reva2\JsonApi\Contracts\Http\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * JSON API service
 *
 * @package Reva2\JsonApi\Contracts\Services
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
interface JsonApiServiceInterface
{
    /**
     * Sets current environment
     *
     * @param EnvironmentInterface $environment
     * @return $this
     */
    public function setEnvironment(EnvironmentInterface $environment);

    /**
     * Returns request media type
     *
     * @return MediaTypeInterface
     */
    public function getRequestMediaType();

    /**
     * Returns response media type
     *
     * @return MediaTypeInterface
     */
    public function getResponseMediaType();

    /**
     * Parse and return API request object
     *
     * @param Request $httpRequest
     * @param bool|array $validate Bool or array of validation groups that should be checked
     * @return RequestInterface
     * @throws JsonApiException
     */
    public function parseRequest(Request $httpRequest, $validate = true);

    /**
     * Validates specified request
     *
     * @param RequestInterface $request
     * @param array|null $groups Array of validation groups that should be checked
     * @return $this
     * @throws JsonApiException
     */
    public function validateRequest(RequestInterface $request, array $groups = null);

    /**
     * Returns response factory
     *
     * @param EncodingParametersInterface $encodingParams
     * @return ResponsesInterface
     */
    public function getResponseFactory(EncodingParametersInterface $encodingParams);
}