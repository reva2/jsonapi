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
use Reva2\JsonApi\Contracts\Decoders\Mapping\ReferenceMetadataInterface;

/**
 * JSON API document metadata
 *
 * @package Reva2\JsonApi\Decoders\Mapping
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class DocumentMetadata extends GenericMetadata implements DocumentMetadataInterface
{
    /**
     * @var ReferenceMetadataInterface
     * @internal
     */
    public $content;

    /**
     * @var bool
     * @internal
     */
    public $allowEmpty = false;

    /**
     * @inheritdoc
     */
    public function getContentMetadata()
    {
        return $this->content;
    }

    /**
     * Sets metadata for document content
     *
     * @param ReferenceMetadataInterface $metadata
     * @return $this
     */
    public function setContentMetadata(ReferenceMetadataInterface $metadata)
    {
        $this->content = $metadata;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function canBeEmpty()
    {
        return $this->allowEmpty;
    }

    /**
     * Sets value of flag which show that document can be empty
     *
     * @param bool $allow
     * @return $this
     */
    public function setCanBeEmpty($allow)
    {
        $this->allowEmpty = $allow;

        return $this;
    }
}