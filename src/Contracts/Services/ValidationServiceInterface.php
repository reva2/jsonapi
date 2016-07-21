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

use Neomerx\JsonApi\Document\Error;

/**
 * Interface for validation service
 *
 * @package Reva2\JsonApi\Contracts\Services
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
interface ValidationServiceInterface
{
    /**
     * Validate specified data
     *
     * @param mixed $data
     * @param array|null $groups
     * @return Error[]
     */
    public function validate($data, array $groups = null);
}
