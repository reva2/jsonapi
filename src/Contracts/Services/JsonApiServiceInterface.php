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

use Neomerx\JsonApi\Contracts\Http\ResponsesInterface;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Reva2\JsonApi\Contracts\Factories\FactoryInterface;
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
     * Returns JSON API factory
     *
     * @return FactoryInterface
     */
    public function getFactory();

    /**
     * Parse and return API request object
     *
     * @param Request $request
     * @param EnvironmentInterface|null $environment
     * @return RequestInterface
     * @throws JsonApiException
     */
    public function parseRequest(Request $request, EnvironmentInterface $environment = null);

    /**
     * Validates specified request
     *
     * @param RequestInterface $request
     * @return $this
     * @throws JsonApiException
     */
    public function validateRequest(RequestInterface $request);

    /**
     * Returns response factory
     *
     * @param RequestInterface $request
     * @return ResponsesInterface
     */
    public function getResponseFactory(RequestInterface $request);

    /**
     * Returns JSON API environment configured in request
     *
     * @param Request $request
     * @return EnvironmentInterface
     */
    public function getRequestEnvironment(Request $request);
}
