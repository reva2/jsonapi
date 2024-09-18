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
class ApiObject
{
    /**
     * Name of field that store discriminator value
     */
    public ?string $discField;

    /**
     * Mapping of discriminator values to classes
     */
    public ?array $discMap;

    /**
     * Error message used when invalid discriminator
     * value specified
     *
     * @var string
     */
    public string $discError;

    /**
     * @param string|null $discField
     * @param array|null $discMap
     * @param string $discError
     */
    public function __construct(
        ?string $discField = null,
        ?array  $discMap = null,
        string  $discError = "Discriminator class for value '{{value}}' not specified")
    {
        $this->discField = $discField;
        $this->discMap = $discMap;
        $this->discError = $discError;
    }


}