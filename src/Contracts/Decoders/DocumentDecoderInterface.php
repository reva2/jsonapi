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
 * Interface for JSON API document decoders
 *
 * @package Reva2\JsonApi\Contracts\Decoders
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
interface DocumentDecoderInterface
{
    /**
     * Decode JSON API document and returns appropriate object
     *
     * @param $data
     * @param DataParserInterface $parser
     * @return mixed
     */
    public function decode($data, DataParserInterface $parser);
}