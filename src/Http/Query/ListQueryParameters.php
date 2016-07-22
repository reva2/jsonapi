<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) OrbitScripts LLC <support@orbitscripts.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Http\Query;

use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use Neomerx\JsonApi\Contracts\Encoder\Parameters\SortParameterInterface;
use Neomerx\JsonApi\Encoder\Parameters\SortParameter;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * JSON API resources list parameters
 *
 * @package Reva2\JsonApi\Http\Query
 * @author Sergey Revenko <reva2@orbita1.ru>
 */
class ListQueryParameters extends QueryParameters
{
    const INVALID_PAGE_SIZE = '400b395f-ee4f-4138-aad8-89f9a1872291';
    const INVALID_SORTING = 'e57fef44-21e4-48c4-a30d-b92009a0c16a';

    /**
     * @var integer|null
     * @Reva2\JsonApi\Annotations\Property(type="integer", path="[page][number]")
     * @Symfony\Component\Validator\Constraints\Type(type="integer")
     * @Symfony\Component\Validator\Constraints\GreaterThan(value=0)
     */
    protected $pageNumber;

    /**
     * @var integer|null
     * @Reva2\JsonApi\Annotations\Property(type="integer", path="[page][size]")
     * @Symfony\Component\Validator\Constraints\Type(type="integer")
     * @Symfony\Component\Validator\Constraints\GreaterThan(value=0)
     */
    protected $pageSize;

    /**
     * @var SortParameterInterface[]|null
     * @Reva2\JsonApi\Annotations\Property(path="[sort]", parser="parseSortingParameters")
     * @Symfony\Component\Validator\Constraints\Type(type="array")
     */
    protected $sortParameters;

    /**
     * @param int|null $pageNumber
     * @return $this
     */
    public function setPageNumber($pageNumber = null)
    {
        $this->pageNumber = $pageNumber;

        return $this;
    }

    /**
     * @param int|null $pageSize
     * @return $this
     */
    public function setPageSize($pageSize = null)
    {
        $this->pageSize = $pageSize;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPaginationParameters()
    {
        return [
            'number' => ($this->pageNumber) ? $this->pageNumber : 1,
            'size' => ($this->pageSize) ? $this->pageSize : $this->getDefaultPageSize()
        ];
    }

    /**
     * Sets sorting parameters
     *
     * @param SortParameterInterface[]|null $sorting
     * @return $this
     */
    public function setSortParameters(array $sorting = null)
    {
        $this->sortParameters = $sorting;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSortParameters()
    {
        return $this->sortParameters;
    }

    /**
     * Parse sorting parameters string
     *
     * @param string $value
     * @return array
     */
    public function parseSortingParameters($value)
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
     * @Symfony\Component\Validator\Constraints\Callback()
     */
    public function validateSortParameters(ExecutionContextInterface $context)
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
            $context
                ->buildViolation('Sorting by following fields is not supported: %fields%')
                ->setParameter('%fields%', sprintf("'%s'", implode("', '", $invalidFields)))
                ->setPlural(count($invalidFields))
                ->setInvalidValue($invalidFields)
                ->setCode(self::INVALID_SORTING)
                ->atPath('sortParameters')
                ->addViolation();
        }
    }

    /**
     * Validate page size
     *
     * @param ExecutionContextInterface $context
     * @Symfony\Component\Validator\Constraints\Callback()
     */
    public function validatePageSize(ExecutionContextInterface $context)
    {
        if (!empty($this->pageSize) &&
            (null !== ($maxSize = $this->getMaxPageSize())) &&
            ($this->pageSize > $maxSize)
        ) {
            $context
                ->buildViolation('Page size must be leas or equal than %size%')
                ->setParameter('%size%', $maxSize)
                ->setInvalidValue($this->pageSize)
                ->setCode(self::INVALID_PAGE_SIZE)
                ->atPath('pageSize')
                ->addViolation();
        }
    }

    /**
     * Returns default page size
     *
     * @return int|null
     */
    protected function getDefaultPageSize()
    {
        return 10;
    }

    /**
     * Returns max page size
     *
     * @return int|null
     */
    protected function getMaxPageSize()
    {
        return 100;
    }

    /**
     * Returns list of supported sorting fields
     *
     * @return string[]
     */
    protected function getSortableFields()
    {
        return [];
    }
}