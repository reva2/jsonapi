<?php

namespace Reva2\JsonApi\Tests\Fixtures\Documents;

use Reva2\JsonApi\Annotations as API;
use Reva2\JsonApi\Tests\Fixtures\Metadata\OfficesListMetadata;
use Reva2\JsonApi\Tests\Fixtures\Resources\Office;

/**
 * @API\ApiDocument()
 */
class OfficesListDocument
{
    /**
     * @var Office[]
     * @API\Content(type="Array<Reva2\JsonApi\Tests\Fixtures\Resources\Office>")
     */
    public $data;

    /**
     * @var OfficesListMetadata
     * @API\Metadata(type="Reva2\JsonApi\Tests\Fixtures\Metadata\OfficesListMetadata")
     */
    public $meta;
}
