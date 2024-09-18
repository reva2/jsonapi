<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Tests\Fixtures\Resources;

use Reva2\JsonApi\Attributes as API;
use Reva2\JsonApi\Tests\Fixtures as Fixtures;
use Reva2\JsonApi\Tests\Fixtures\Converters\StoreConverter;

/**
 * Example JSON API resource that represent pet
 *
 * @package Reva2\JsonApi\Tests\Fixtures\Resources
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
#[API\Resource(
    type: "pets",
    discField: "family",
    discMap: [
        "cats" => Cat::class,
        "dogs" => Dog::class
    ]
)]
class Pet
{
    /**
     * @var ?string
     */
    #[API\Id]
    public ?string $id;

    /**
     * @var ?string
     */
    #[API\Attribute]
    public ?string $name;

    /**
     * @var ?string
     */
    #[API\Attribute]
    public ?string $family;

    /**
     * @var ?Store
     */
    #[API\Relationship(
        type: Store::class,
        converter: StoreConverter::class. '::convert'
    )]
    public ?Store $store;

    /**
     * @var array|Person[]
     */
    #[API\Relationship(type: Person::class . '[]')]
    public array $owners = [];

    /**
     * Virtual attribute
     *
     * @var ?string
     */
    protected ?string $virtualAttr;

    /**
     * Virtual relationship
     *
     * @var ?Something
     */
    protected ?Something $virtualRel;

    /**
     * @return ?string
     */
    public function getVirtualAttr(): ?string
    {
        return $this->virtualAttr;
    }

    /**
     * @param string $virtualAttr
     * @return Pet
     * @API\VirtualAttribute(name="virtualAttr", type="string")
     */
    #[API\VirtualAttribute(name: "virtualAttr", type: "string")]
    public function setVirtualAttr(?string $virtualAttr): self
    {
        $this->virtualAttr = $virtualAttr;

        return $this;
    }

    /**
     * @return string
     */
    public function whoIAm(): string
    {
        return 'pet';
    }

    /**
     * @return ?Something
     */
    public function getVirtualRel(): ?Something
    {
        return $this->virtualRel;
    }

    /**
     * @param ?Something $virtualRel
     * @return $this
     */
    #[API\VirtualRelationship(name: "virtualRel", type: Something::class)]
    public function setVirtualRel(?Something $virtualRel): self
    {
        $this->virtualRel = $virtualRel;

        return $this;
    }
}
