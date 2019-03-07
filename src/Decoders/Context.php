<?php
/*
 * This file is part of the jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reva2\JsonApi\Decoders;

/**
 * Decoder context
 *
 * @author Sergey Revenko <dedsemen@gmail.com>
 * @package Reva2\JsonApi\Decoders
 */
class Context
{
    /**
     * @var array
     */
    protected $resources = [];

    /**
     * @var array
     */
    protected $linked = [];

    /**
     * Register resource with specified type and id
     *
     * @param string $type
     * @param string $id
     * @param mixed $resource
     * @return self
     */
    public function registerResource($type, $id, $resource)
    {
        if (!isset($this->resources[$type])) {
            $this->resources[$type] = [];
        }

        if (isset($this->resources[$type][$id])) {
            throw new \RuntimeException(sprintf(
                "Resource with type '%s' and id '%s' already registered",
                $type,
                $id
            ));
        }

        $this->resources[$type][$id] = $resource;

        return $this;
    }

    /**
     * Returns resource with specified type and id
     *
     * @param string $type
     * @param string $id
     * @return mixed|null
     */
    public function getResource($type, $id)
    {
        if (!isset($this->resources[$type][$id])) {
            return null;
        }

        return $this->resources[$type][$id];
    }

    /**
     * Adds linked data
     *
     * @param string $type
     * @param string $id
     * @param int $idx
     * @param mixed $data
     * @return $this
     */
    public function addLinkedData($type, $id, $idx, $data)
    {
        if (!isset($this->linked[$type])) {
            $this->linked[$type] = [];
        }

        $this->linked[$type][$id] = ['idx' => $idx, 'data' => $data];

        return $this;
    }

    /**
     * Returns linked data for resource with specified type and id
     *
     * @param $type
     * @param $id
     * @return array|null
     */
    public function getLinkedData($type, $id)
    {
        if (!isset($this->linked[$type][$id])) {
            return null;
        }

        return $this->linked[$type][$id]['data'];
    }

    public function getLinkedDataIndex($type, $id)
    {
        if (!isset($this->linked[$type][$id])) {
            return null;
        }

        return $this->linked[$type][$id]['idx'];
    }
}
