<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Decoders;

use Reva2\JsonApi\Contracts\Decoders\Data\AttributesInterface;
use Reva2\JsonApi\Contracts\Decoders\Data\RelationshipsInterface;
use Reva2\JsonApi\Contracts\Decoders\Data\ResourceInterface;
use Reva2\JsonApi\Contracts\Decoders\DataParserInterface;
use Reva2\JsonApi\Contracts\Decoders\ResourceDecoderInterface;


/**
 * Base class for JSON API resource decoders
 *
 * @package Reva2\JsonApi\Decoders
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
abstract class ResourceDecoder implements ResourceDecoderInterface
{
    /**
     * @inheritdoc
     */
    public function decode($data, DataParserInterface $parser)
    {
        $this->checkResourceType($data, $parser);
        
        $resource = $this->createResource();
        if (!$resource instanceof ResourceInterface) {
            throw new \OutOfBoundsException(
                sprintf("Object must implement %s interface", ResourceInterface::class),
                500
            );
        }
        
        if ($parser->hasValue($data, 'id')) {
            $resource->setId($parser->parseString($data, 'id'));
        }
        
        if ($parser->hasValue($data, 'attributes')) {
            $parser->setPath('attributes');

            $attributes = $this->parseAttributes($parser->getValue($data, 'attributes'), $parser);
            if (!$attributes instanceof AttributesInterface) {
                throw new \OutOfBoundsException(
                    sprintf("Object must implement %s interface", AttributesInterface::class),
                    500
                );
            }
            
            $resource->setAttributes($attributes);

            $parser->restorePath();
        }

        if ($parser->hasValue($data, 'relationships')) {
            $parser->setPath('relationships');

            $relationships = $this->parseRelationships($parser->getValue($data, 'relationships'), $parser);
            if (!$relationships instanceof RelationshipsInterface) {
                throw new \OutOfBoundsException(
                    sprintf("Object must implement %s interface", RelationshipsInterface::class),
                    500
                );
            }
            
            $resource->setRelationships($relationships);
            
            $parser->restorePath();
        }
        
        return $resource;
    }

    /**
     * Returns type of supported resources
     *
     * @return string
     */
    abstract protected function getResourceType();

    /**
     * Create object that represent resource
     *
     * @return ResourceInterface
     */
    abstract protected function createResource();

    /**
     * Parse resource attributes
     *
     * @param object|array $data
     * @param DataParserInterface $decoder
     * @return AttributesInterface
     */
    abstract protected function parseAttributes($data, DataParserInterface $decoder);

    /**
     * Parse resource relationships
     *
     * @param object|array $data
     * @param DataParserInterface $decoder
     * @return RelationshipsInterface
     */
    abstract protected function parseRelationships($data, DataParserInterface $decoder);

    /**
     * Checks that data type match to supported resource type
     *
     * @param object|array $data
     * @param DataParserInterface $decoder
     */
    private function checkResourceType($data, DataParserInterface $decoder)
    {
        $type = $decoder->parseString($data, 'type');
        if ($type !== $this->getResourceType()) {
            throw new \InvalidArgumentException(
                sprintf("Value expected to be resource of type %s", $this->getResourceType()),
                409
            );
        }
    }
}