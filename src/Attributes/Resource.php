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

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Resource extends ApiObject
{
    /**
     * Resource type
     */
    public ?string $type;

    /**
     * Loader used for resources of this type
     */
    public ?string $loader;

    public function __construct(
        ?string $type = null,
        ?string $loader = null,
        ?string $discField = null,
        ?array  $discMap = null,
        string  $discError = "Discriminator class for value '{{value}}' not specified")
    {
        parent::__construct($discField, $discMap, $discError);

        $this->type = $type;
        $this->loader = $loader;
    }
}