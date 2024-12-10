<?php

namespace Reva2\JsonApi\Contracts\Encoder;

interface SortParameterInterface
{
    /**
     * Get sort field name.
     *
     * @return string
     */
    public function getField(): string;

    /**
     * Get true if parameter is ascending.
     *
     * @return bool
     */
    public function isAscending(): bool;

    /**
     * Get true if parameter is descending.
     *
     * @return bool
     */
    public function isDescending(): bool;
}