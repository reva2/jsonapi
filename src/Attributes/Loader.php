<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reva2\JsonApi\Attributes;

class Loader
{
    /**
     * @var string
     */
    public string $loader;

    /**
     * Serialization group
     *
     * @var string
     */
    public string $group = 'Default';

    /**
     * @param string $loader
     * @param string $group
     */
    public function __construct(string $loader, string $group = 'Default')
    {
        $this->loader = $loader;
        $this->group = $group;
    }
}