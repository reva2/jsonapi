<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Contracts\Factories;

use Neomerx\JsonApi\Contracts\Factories\FactoryInterface as BaseFactory;
use Reva2\JsonApi\Contracts\Http\RequestInterface;
use Reva2\JsonApi\Contracts\Services\EnvironmentInterface;

/**
 * Factory interface
 *
 * @package Reva2\JsonApi\Contracts\Factories
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
interface FactoryInterface extends BaseFactory
{
    /**
     * Create JSON API environment
     *
     * @param array|null $config
     * @return EnvironmentInterface
     */
    public function createEnvironment(array $config = null);

    /**
     * Create JSON API request object
     *
     * @param EnvironmentInterface $environment
     * @return RequestInterface
     */
    public function createRequest(EnvironmentInterface $environment);
}
