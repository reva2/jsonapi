<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Decoders;

use Reva2\JsonApi\Contracts\Decoders\CallbackResolverInterface;

/**
 * Simple callback resolver
 *
 * @package Reva2\JsonApi\Decoders
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class CallbackResolver implements CallbackResolverInterface
{
    const STATIC_METHOD_PATTERN = "/[A-Za-z0-9\._\-]+::[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/";

    /**
     * @inheritdoc
     */
    public function resolveCallback($name)
    {
        if (preg_match(static::STATIC_METHOD_PATTERN, $name)) {
            list($class, $method) = explode('::', $name, 2);
            $callback = array($class, $method);
        } else {
            $callback = $name;
        }

        if (!is_callable($callback)) {
            throw new \InvalidArgumentException(sprintf("'%s' is not callable", $name));
        }

        return $callback;
    }
}
