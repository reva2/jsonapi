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

use Reva2\JsonApi\Contracts\Decoders\DecodersFactoryInterface;
use Reva2\JsonApi\Contracts\Decoders\DocumentDecoderInterface;
use Reva2\JsonApi\Contracts\Decoders\QueryParamsDecoderInterface;
use Reva2\JsonApi\Contracts\Decoders\ResourceDecoderInterface;

/**
 * JSON API decoders factory
 *
 * @package Reva2\JsonApi\Decoders
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class DecodersFactory implements DecodersFactoryInterface
{
    /**
     * Decoders map
     *
     * @var array
     */
    protected $map;

    /**
     * Decoders instances
     *
     * @var array
     */
    protected $instances;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->map = [
            'doc' => [],
            'res' => [],
            'query' => []
        ];

        $this->instances = [
            'doc' => [],
            'res' => [],
            'query' => []
        ];
    }

    /**
     * Register decoder for JSON API resource
     *
     * @param string $resType
     * @param string|\Closure $decoder
     * @return DecodersFactory
     */
    public function registerResourceDecoder($resType, $decoder)
    {
        return $this->registerDecoderByCategory('res', $resType, $decoder);
    }

    /**
     * Register decoder for JSON API document
     *
     * @param $docType
     * @param $decoder
     * @return DecodersFactory
     */
    public function registerDocumentDecoder($docType, $decoder)
    {
        return $this->registerDecoderByCategory('doc', $docType, $decoder);
    }

    /**
     * Register decoder for JSON API request query parameter
     * 
     * @param string $queryType
     * @param string|\Closure $decoder
     * @return DecodersFactory
     */
    public function registerQueryParamsDecoder($queryType, $decoder)
    {
        return $this->registerDecoderByCategory('query', $queryType, $decoder);
    }

    /**
     * @inheritdoc
     */
    public function getResourceDecoder($type)
    {
        return $this->getDecoderInstanceByCategory('res', $type);
    }

    /**
     * @inheritdoc
     */
    public function getDocumentDecoder($type)
    {
        return $this->getDecoderInstanceByCategory('doc', $type);
    }

    /**
     * @inheritdoc
     */
    public function getQueryParamsDecoder($type)
    {
        return $this->getDecoderInstanceByCategory('query', $type);
    }

    /**
     * Register decoder in specified category
     *
     * @param string $category
     * @param string $resType
     * @param string|\Closure $decoder
     * @return $this
     */
    private function registerDecoderByCategory($category, $resType, $decoder)
    {
        if ((!is_string($decoder)) && (!$decoder instanceof \Closure)) {
            throw new \InvalidArgumentException('Decoder must be a string containing class name or \Closure instance');
        }
        
        $this->map[$category][$resType] = $decoder;
        
        return $this;
    }

    /**
     * Returns decoder instance for specified type from specified category
     *
     * @param string $category
     * @param string $type
     * @return mixed
     */
    private function getDecoderInstanceByCategory($category, $type)
    {
        if (!array_key_exists($type, $this->instances[$category])) {
            if (!array_key_exists($type, $this->map[$category])) {
                throw new \RuntimeException(sprintf("Decoder for type '%s' is not registered", $type));
            }

            $this->instances[$category][$type] = $this->createDecoder($category, $this->map[$category][$type]);
        }

        return $this->instances[$category][$type];
    }

    /**
     * Create decoder instance
     *
     * @param string $category
     * @param string|\Closure $decoder
     * @return mixed
     */
    private function createDecoder($category, $decoder)
    {
        $decoder = (is_string($decoder)) ? new $decoder() : $decoder();
        
        if (('res' === $category) && (!$decoder instanceof ResourceDecoderInterface)) {
            throw new \LogicException(sprintf(
                "Resource decoder must implement %s interface",
                ResourceDecoderInterface::class
            ));
        } elseif (('query' === $category) && (!$decoder instanceof QueryParamsDecoderInterface)) {
            throw new \LogicException(sprintf(
                "Query parameters decoder must implement %s interface",
                QueryParamsDecoderInterface::class
            ));
        } elseif (('doc' === $category) && (!$decoder instanceof DocumentDecoderInterface)) {
            throw new \LogicException(sprintf(
                "Document decoder must implement %s interface",
                DocumentDecoderInterface::class
            ));
        }

        return $decoder;
    }
}