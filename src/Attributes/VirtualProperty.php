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
use InvalidArgumentException;

#[Attribute(Attribute::TARGET_METHOD)]
class VirtualProperty extends Property
{
    public string $name;

    public function __construct(
        string  $name,
        ?string $type = null,
        ?bool   $multiple = false,
        ?string $path = null,
        ?string $parser = null,
        ?string $setter = null,
        ?string $converter = null,
        array   $groups = ['Default'],
        array $loaders = []
    )
    {
        parent::__construct($type, $multiple, $path, $parser, $setter, $converter, $groups, $loaders);

        $this->name = $name;
    }
}