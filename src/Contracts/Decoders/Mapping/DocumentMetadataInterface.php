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
 * JSON API document metadata
 *
 * @package Reva2\JsonApi\Contracts\Decoders\Mapping
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
interface DocumentMetadataInterface extends GenericMetadataInterface
{
    /**
     * Returns metadata data for document' content
     * 
     * @return PropertyMetadataInterface
     */
    public function getContentMetadata();

    /**
     * Returns whether document can be empty
     * 
     * @return bool
     */
    public function isAllowEmpty();
}