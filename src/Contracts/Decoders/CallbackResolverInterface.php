<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Contracts\Decoders;

/**
 * Callback resolver
 *
 * @package Reva2\JsonApi\Contracts\Decoders
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
interface CallbackResolverInterface
{
    /**
     * Returns a callable given its string representation
     *
     * @param string $name
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function resolveCallback($name);
}
