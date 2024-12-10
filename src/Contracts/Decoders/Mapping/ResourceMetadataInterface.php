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
 * JSON API resource metadata
 *
 * @package Reva2\JsonApi\Contracts\Decoders\Mapping
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
interface ResourceMetadataInterface extends ClassMetadataInterface
{
    /**
     * Returns resource name
     *
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * Returns resource loader
     *
     * @return string|null
     */
    public function getLoader(): ?string;

    /**
     * @return PropertyMetadataInterface|null
     */
    public function getIdMetadata(): ?PropertyMetadataInterface;

    /**
     * Returns metadata resource attributes
     *
     * @return PropertyMetadataInterface[]
     */
    public function getAttributes(): array;

    /**
     * Returns metadata for resource relationships
     *
     * @return PropertyMetadataInterface[]
     */
    public function getRelationships(): array;
}
