<?php
/*
 * This file is part of the jsonapi.
 *
 * (c) OrbitScripts LLC <support@orbitscripts.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reva2\JsonApi\Contracts\Decoders;

use Reva2\JsonApi\Contracts\Decoders\Mapping\ResourceMetadataInterface;

/**
 * Basic interface for JSON API resource loaders
 *
 * @author Sergey Revenko <sergey.revenko@orbitsoft.com>
 * @package Reva2\JsonApi\Contracts\Decoders
 */
interface ResourceLoaderInterface
{
    /**
     * Load specified resource
     *
     * @param string $id
     * @param ResourceMetadataInterface $metadata
     * @return mixed|null
     */
    public function load($id, ResourceMetadataInterface $metadata);
}
