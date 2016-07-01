<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Contracts\Decoders;

/**
 * Interface for JSON API resources decoders factory
 *
 * @package Reva2\JsonApi\Contracts\Decoders
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
interface DecodersFactoryInterface
{
    /**
     * Returns decoder for specified resource type
     *
     * @param string $type
     * @return ResourceDecoderInterface
     */
    public function getResourceDecoder($type);

    /**
     * Returns decoder for specified document type
     *
     * @param string $type
     * @return DocumentDecoderInterface
     */
    public function getDocumentDecoder($type);

    /**
     * Returns decoder for specified request query parameters
     *
     * @param string $type
     * @return QueryParamsDecoderInterface
     */
    public function getQueryParamsDecoder($type);
}