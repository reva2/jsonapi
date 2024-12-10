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
    protected array $resources = [];

    /**
     * @var array
     */
    protected array $linked = [];

    /**
     * Register resource with specified type and id
     *
     * @param string $type
     * @param string $id
     * @param mixed $resource
     * @return self
     */
    public function registerResource(string $type, string $id, mixed $resource): self
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
    public function getResource(string $type, string $id): mixed
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
    public function addLinkedData(string $type, string $id, int $idx, mixed $data): self
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
     * @param string|null $type
     * @param string|null $id
     * @return mixed|null
     */
    public function getLinkedData(?string $type, ?string $id): mixed
    {
        if (!isset($this->linked[$type][$id])) {
            return null;
        }

        return $this->linked[$type][$id]['data'];
    }

    /**
     * @param string $type
     * @param string $id
     * @return array|null
     */
    public function getLinkedDataIndex(string $type, string $id): ?int
    {
        if (!isset($this->linked[$type][$id])) {
            return null;
        }

        return $this->linked[$type][$id]['idx'];
    }
}
