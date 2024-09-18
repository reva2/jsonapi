<?php

namespace Reva2\JsonApi\Tests\Fixtures\Documents;

use Reva2\JsonApi\Attributes as API;
use Reva2\JsonApi\Tests\Fixtures\Metadata\OfficesListMetadata;
use Reva2\JsonApi\Tests\Fixtures\Resources\Office;

/**
 * @API\ApiDocument()
 */
#[API\Document]
class OfficesListDocument
{
    /**
     * @var Office[]
     */
    #[API\Content("Array<Reva2\JsonApi\Tests\Fixtures\Resources\Office>")]
    public ?array $data;

    /**
     * @var ?OfficesListMetadata
     */
    #[API\Metadata(OfficesListMetadata::class)]
    public ?OfficesListMetadata $meta;
}
