<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Contracts\Http\Query;

use Reva2\JsonApi\Contracts\Decoders\DataParserInterface;

/**
 * Query parameters parser interface
 *
 * @package Reva2\JsonApi\Contracts\Http\Query
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
interface QueryParametersParserInterface
{
    /**
     * Sets data parser
     *
     * @param DataParserInterface $parser
     * @return $this
     */
    public function setDataParser(DataParserInterface $parser);

    /**
     * @param string $type
     * @return $this
     */
    public function setQueryType($type);
}
