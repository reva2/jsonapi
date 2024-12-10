<?php

namespace Reva2\JsonApi\Contracts\Encoder;

interface EncodingParametersInterface
{
    /**
     * Get requested include paths.
     *
     * @return array|null
     */
    public function getIncludePaths(): ?array;

    /**
     * Get field names that should be in result.
     *
     * @return array|null
     */
    public function getFieldSets(): ?array;

    /**
     * Get field names that should be in result.
     *
     * @param string $type
     *
     * @return string[]|null
     */
    public function getFieldSet(string $type): ?array;

    /**
     * Get sort parameters.
     *
     * @return SortParameterInterface[]|null
     */
    public function getSortParameters(): ?array;

    /**
     * Get pagination parameters.
     *
     * Pagination parameters are not detailed in the specification however a keyword 'page' is reserved for pagination.
     * This method returns key and value pairs from input 'page' parameter.
     *
     * @return array|null
     */
    public function getPaginationParameters(): ?array;

    /**
     * Returns true if inclusion, field set, sorting, paging, and filtering parameters are empty.
     *
     * @return bool
     */
    public function isEmpty(): bool;
}