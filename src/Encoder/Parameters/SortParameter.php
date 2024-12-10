<?php

namespace Reva2\JsonApi\Encoder\Parameters;

class SortParameter
{
    /**
     * @var string
     */
    private string $sortField;

    /**
     * @var bool
     */
    private bool $isAscending;

    public function __construct(string $sortField, bool $isAscending)
    {
        $this->sortField = $sortField;
        $this->isAscending = $isAscending;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $prefix = $this->isAscending() ? '' : '-';

        return $prefix . $this->getField();
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->sortField;
    }

    /**
     * @return bool
     */
    public function isAscending(): bool
    {
        return $this->isAscending;
    }

    /**
     * @return bool
     */
    public function isDescending(): bool
    {
        return !$this->isAscending;
    }
}