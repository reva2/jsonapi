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

use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;

/**
 * Interface for JSON API requests parser
 *
 * @package Reva2\JsonApi\Contracts\Decoders
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
interface DataParserInterface
{
    /**
     * Parse JSON API document
     *
     * @param object|array $data
     * @param string $docType
     * @return mixed
     */
    public function parseDocument($data, $docType);

    /**
     * Parse JSON API request query parameters
     *
     * @param object|array $data
     * @param string $paramsType
     * @return EncodingParametersInterface
     */
    public function parseQueryParams($data, $paramsType);

    /**
     * Sets serialization groups
     *
     * @param string[] $groups
     * @return DataParserInterface
     */
    public function setSerializationGroups(array $groups);

    /**
     * Returns serialization groups
     *
     * @return string[]
     */
    public function getSerializationGroups();
}
