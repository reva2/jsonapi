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

use Reva2\JsonApi\Contracts\Decoders\Data\ResourceInterface;

/**
 * Interface for JSON API resource decoders
 *
 * @package Reva2\JsonApi\Contracts\Decoders
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
interface ResourceDecoderInterface
{
    /**
     * Decode resource and return appropriate object
     *
     * @param object|array $data
     * @param DataParserInterface $parser
     * @return ResourceInterface
     */
    public function decode($data, DataParserInterface $parser);
}