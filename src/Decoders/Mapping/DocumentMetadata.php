<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Decoders\Mapping;

use Reva2\JsonApi\Contracts\Decoders\Mapping\DocumentMetadataInterface;
use Reva2\JsonApi\Contracts\Decoders\Mapping\PropertyMetadataInterface;

/**
 * JSON API document metadata
 *
 * @package Reva2\JsonApi\Decoders\Mapping
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class DocumentMetadata extends GenericMetadata implements DocumentMetadataInterface
{
    /**
     * @var PropertyMetadataInterface|null
     * @internal
     */
    public ?PropertyMetadataInterface $content = null;

    /**
     * @var PropertyMetadataInterface|null
     * @internal
     */
    public ?PropertyMetadataInterface $metadata = null;

    /**
     * @var bool
     * @internal
     */
    public bool $allowEmpty = false;

    /**
     * @inheritdoc
     */
    public function getContentMetadata(): ?PropertyMetadataInterface
    {
        return $this->content;
    }

    /**
     * Sets metadata for document content
     *
     * @param PropertyMetadataInterface $metadata
     * @return $this
     */
    public function setContentMetadata(PropertyMetadataInterface $metadata): self
    {
        $this->content = $metadata;

        return $this;
    }

    /**
     * @return PropertyMetadataInterface|null
     */
    public function getMetadata(): ?PropertyMetadataInterface
    {
        return $this->metadata;
    }

    /**
     * @param PropertyMetadataInterface $metadata
     * @return DocumentMetadata
     */
    public function setMetadata(PropertyMetadataInterface $metadata): self
    {
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isAllowEmpty(): bool
    {
        return $this->allowEmpty;
    }

    /**
     * Sets value of flag which show that document can be empty
     *
     * @param bool $allow
     * @return $this
     */
    public function setAllowEmpty(bool $allow): self
    {
        $this->allowEmpty = $allow;

        return $this;
    }
}
