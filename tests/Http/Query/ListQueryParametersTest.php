<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Tests\Http\Query;

use Reva2\JsonApi\Http\Query\ListQueryParameters;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

/**
 * Test for JSON API list request query parameters
 *
 * @package Reva2\JsonApi\Tests\Http\Query
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class ListQueryParametersTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function shouldHaveDefaultPaginationParameters()
    {
        $query = new ListQueryParameters();

        $this->assertSame(['number' => 1, 'size' => 10], $query->getPaginationParameters());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionCode 400
     */
    public function shouldThrowOnInvalidSortParameters()
    {
        $query = new ListQueryParameters();
        $query->parseSortingParameters(null);
        $query->parseSortingParameters(['invalid']);
    }

    /**
     * @test
     */
    public function shouldValidatePageSize()
    {
        $violation = $this->getMockBuilder(ConstraintViolationBuilderInterface::class)->getMock();
        $violation
            ->expects($this->once())
            ->method('setParameters')
            ->with(['%size%' => 100])
            ->willReturnSelf();

        $violation
            ->expects($this->once())
            ->method('setInvalidValue')
            ->with(125)
            ->willReturnSelf();

        $violation
            ->expects($this->once())
            ->method('setCode')
            ->with(ListQueryParameters::INVALID_PAGE_SIZE)
            ->willReturnSelf();

        $violation
            ->expects($this->once())
            ->method('atPath')
            ->with('pageSize')
            ->willReturnSelf();

        $violation->expects($this->once())->method('addViolation');

        $context = $this->getMockBuilder(ExecutionContextInterface::class)->getMock();
        $context
            ->expects($this->once())
            ->method('buildViolation')
            ->with('Page size must be leas or equal than %size%')
            ->willReturn($violation);

        $query = new ListQueryParameters();
        $query->validatePageSize($context);

        $query->setPageSize(125)->validatePageSize($context);
    }

    /**
     * @test
     */
    public function shouldValidateSortingParameters()
    {
        $violation = $this->getMockBuilder(ConstraintViolationBuilderInterface::class)->getMock();
        $violation
            ->expects($this->once())
            ->method('setParameters')
            ->with(['%fields%' => "'store.name', 'name'"])
            ->willReturnSelf();

        $violation
            ->expects($this->once())
            ->method('setPlural')
            ->with(2)
            ->willReturnSelf();

        $violation
            ->expects($this->once())
            ->method('setInvalidValue')
            ->with(['store.name', 'name'])
            ->willReturnSelf();

        $violation
            ->expects($this->once())
            ->method('setCode')
            ->with(ListQueryParameters::INVALID_SORTING)
            ->willReturnSelf();

        $violation
            ->expects($this->once())
            ->method('atPath')
            ->with('sortParameters')
            ->willReturnSelf();

        $violation->expects($this->once())->method('addViolation');

        $context = $this->getMockBuilder(ExecutionContextInterface::class)->getMock();
        $context
            ->expects($this->once())
            ->method('buildViolation')
            ->with('Sorting by following fields is not supported: %fields%')
            ->willReturn($violation);

        $query = new ListQueryParameters();
        $query->validateSortParameters($context);

        $query->setSortParameters($query->parseSortingParameters('-store.name,name'))->validateSortParameters($context);
    }
}
