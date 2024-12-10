<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Contracts\Decoders\Mapping;

/**
 * Base class metadata interface
 *
 * @package Reva2\JsonApi\Contracts\Decoders\Mapping
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
interface ClassMetadataInterface extends GenericMetadataInterface
{
    /**
     * Returns name of field that store discriminator value
     *
     * @return PropertyMetadataInterface|null
     */
    public function getDiscriminatorField(): ?PropertyMetadataInterface;

    /**
     * Returns class name that corresponds to specified
     * discriminator value
     *
     * @param string $value
     * @return string
     */
    public function getDiscriminatorClass(string $value): string;

    /**
     * Merge parent object metadata
     *
     * @param mixed $metadata
     */
    public function mergeMetadata(mixed $metadata): self;
}
