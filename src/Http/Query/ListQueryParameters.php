<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Http\Query;

use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use Reva2\JsonApi\Contracts\Encoder\SortParameterInterface;
use Reva2\JsonApi\Encoder\Parameters\SortParameter;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Reva2\JsonApi\Attributes as API;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * JSON API resources list parameters
 *
 * @package Reva2\JsonApi\Http\Query
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class ListQueryParameters extends QueryParameters
{
    const INVALID_PAGE_SIZE = '400b395f-ee4f-4138-aad8-89f9a1872291';
    const INVALID_SORTING = 'e57fef44-21e4-48c4-a30d-b92009a0c16a';

    /**
     * @var integer|null
     */
    #[API\Property(path: '[page][number]')]
    #[Assert\Type('integer')]
    #[Assert\GreaterThan(value: 0)]
    protected ?int $pageNumber = null;

    /**
     * @var integer|null
     */
    #[API\Property(path: '[page][size]')]
    #[Assert\Type('integer')]
    #[Assert\GreaterThan(value: 0)]
    protected ?int $pageSize = null;

    /**
     * @var SortParameter[]|null
     */
    #[API\Property(path: '[sort]', parser: 'parseSortingParameters')]
    #[Assert\Type('array')]
    protected ?array $sortParameters = null;

    /**
     * @param int|null $pageNumber
     * @return $this
     */
    public function setPageNumber(?int $pageNumber = null): self
    {
        $this->pageNumber = $pageNumber;

        return $this;
    }

    /**
     * @param int|null $pageSize
     * @return $this
     */
    public function setPageSize(?int $pageSize = null): self
    {
        $this->pageSize = $pageSize;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPaginationParameters(): ?array
    {
        return [
            'number' => ($this->pageNumber) ?: 1,
            'size' => ($this->pageSize) ?: $this->getDefaultPageSize()
        ];
    }

    /**
     * Sets sorting parameters
     *
     * @param SortParameterInterface[]|null $sorting
     * @return $this
     */
    public function setSortParameters(array $sorting = null): self
    {
        $this->sortParameters = $sorting;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSortParameters(): ?array
    {
        return $this->sortParameters;
    }

    /**
     * Parse sorting parameters string
     *
     * @param ?string $value
     * @return ?array
     */
    public function parseSortingParameters($value): ?array
    {
        if (empty($value)) {
            return null;
        }

        if (!is_string($value)) {
            throw new InvalidArgumentException('Sorting parameters must be a string', 400);
        }

        $sorting = [];

        $fields = explode(',', $value);
        foreach ($fields as $field) {
            $isAsc = ('-' !== $field[0]) ? true : false;
            if (false === $isAsc) {
                $field = mb_substr($field, 1);
            }

            $sorting[] = new SortParameter($field, $isAsc);
        }

        return (!empty($sorting)) ? $sorting : null;
    }

    /**
     * Validate sort parameters
     *
     * @param ExecutionContextInterface $context
     */
    #[Assert\Callback()]
    public function validateSortParameters(ExecutionContextInterface $context): void
    {
        if (empty($this->sortParameters)) {
            return;
        }

        $fields = [];
        foreach ($this->sortParameters as $parameter) {
            $fields[] = $parameter->getField();
        }

        $invalidFields = array_diff($fields, $this->getSortableFields());
        if (count($invalidFields) > 0) {
            $this->addViolation(
                $context,
                'Sorting by following fields is not supported: %fields%',
                ['%fields%' => sprintf("'%s'", implode("', '", $invalidFields))],
                $invalidFields,
                self::INVALID_SORTING,
                'sortParameters',
                count($invalidFields)
            );
        }
    }

    /**
     * Validate page size
     *
     * @param ExecutionContextInterface $context
     */
    #[Assert\Callback()]
    public function validatePageSize(ExecutionContextInterface $context): void
    {
        if (!empty($this->pageSize) &&
            (null !== ($maxSize = $this->getMaxPageSize())) &&
            ($this->pageSize > $maxSize)
        ) {
            $this->addViolation(
                $context,
                'Page size must be leas or equal than %size%',
                ['%size%' => $maxSize],
                $this->pageSize,
                self::INVALID_PAGE_SIZE,
                'pageSize'
            );
        }
    }

    /**
     * Returns default page size
     *
     * @return int|null
     */
    protected function getDefaultPageSize(): ?int
    {
        return 10;
    }

    /**
     * Returns max page size
     *
     * @return int|null
     */
    protected function getMaxPageSize(): ?int
    {
        return 100;
    }

    /**
     * Returns list of supported sorting fields
     *
     * @return string[]
     */
    protected function getSortableFields(): array
    {
        return [];
    }
}
