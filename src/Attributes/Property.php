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

#[Attribute(Attribute::TARGET_PROPERTY)]
class Property
{
    /**
     * Data type
     */
    public ?string $type;

    public ?bool $multiple;

    /**
     * Data path
     */
    public ?string $path;

    /**
     * Method used parse property value
     */
    public ?string $parser;

    /**
     * Method used to set property value
     */
    public ?string $setter;

    /**
     * Value converter
     */
    public ?string $converter;

    /**
     * Serialization groups
     */
    public array $groups = ['Default'];

    /**
     * @var Loader[]
     */
    public array $loaders = [];

    public function __construct(
        ?string $type = null,
        ?bool $multiple = false,
        ?string $path = null,
        ?string $parser = null,
        ?string $setter = null,
        ?string $converter = null,
        array  $groups = ['Default'],
        array $loaders = [])
    {
        $this->type = $type;
        $this->multiple = $multiple;
        $this->path = $path;
        $this->parser = $parser;
        $this->setter = $setter;
        $this->converter = $converter;
        $this->groups = $groups;
        $this->loaders = $loaders;

        $this->validate();
    }

    private function validate(): void
    {
        $loadersMap = [];

        foreach ($this->loaders as $loader) {
            if (!$loader instanceof Loader) {
                throw new InvalidArgumentException(sprintf("Loader must be an instance of %s", Loader::class));
            }

            if (array_key_exists($loader->group, $loadersMap)) {
                throw new InvalidArgumentException(sprintf(
                    "Only one loader for serialization group '%s' can be specified",
                    $loader->group
                ));
            }

            $loadersMap[$loader->group] = true;
        }
    }
}