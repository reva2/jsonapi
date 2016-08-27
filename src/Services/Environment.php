<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Services;

use Neomerx\JsonApi\Contracts\Encoder\EncoderInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\MediaTypeInterface;
use Reva2\JsonApi\Contracts\Decoders\DecoderInterface;
use Reva2\JsonApi\Contracts\Services\EnvironmentInterface;

/**
 * JSON API environment
 *
 * @package Reva2\JsonApi\Services
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class Environment implements EnvironmentInterface
{
    /**
     * Expected query parameters type
     *
     * @var string|null
     */
    protected $queryType;

    /**
     * Expected body content type
     *
     * @var string|null
     */
    protected $bodyType;

    /**
     * Codec matcher configuration
     *
     * @var array
     */
    protected $matcherConfiguration;

    /**
     * URLs prefix
     *
     * @var string
     */
    protected $urlPrefix = '';

    /**
     * Validation groups
     *
     * @var string[]|null
     */
    protected $validationGroups;

    /**
     * Request decoder
     *
     * @var DecoderInterface|null
     */
    protected $decoder;

    /**
     * Response encoder
     *
     * @var EncoderInterface|null
     */
    protected $encoder;

    /**
     * Response encoder media type
     *
     * @var MediaTypeInterface|null
     */
    protected $encoderMediaType;

    /**
     * Constructor
     *
     * @param array|null $config
     */
    public function __construct(array $config = null)
    {
        if (null !== $config) {
            $this->fromArray($config);
        }
    }

    /**
     * @inheritdoc
     */
    public function getQueryType()
    {
        return $this->queryType;
    }

    /**
     * Sets expected query parameters type
     *
     * @param null|string $queryType
     * @return $this
     */
    public function setQueryType($queryType = null)
    {
        $this->queryType = $queryType;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBodyType()
    {
        return $this->bodyType;
    }

    /**
     * Sets expected body content type
     *
     * @param null|string $bodyType
     * @return $this
     */
    public function setBodyType($bodyType = null)
    {
        $this->bodyType = $bodyType;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMatcherConfiguration()
    {
        return $this->matcherConfiguration;
    }

    /**
     * Sets codec matcher configuration
     *
     * @param array $matcherConfiguration
     * @return $this
     */
    public function setMatcherConfiguration(array $matcherConfiguration)
    {
        $this->matcherConfiguration = $matcherConfiguration;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUrlPrefix()
    {
        return $this->urlPrefix;
    }

    /**
     * Sets URLs prefix
     *
     * @param string $urlPrefix
     * @return $this
     */
    public function setUrlPrefix($urlPrefix)
    {
        $this->urlPrefix = $urlPrefix;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getValidationGroups()
    {
        return $this->validationGroups;
    }

    /**
     * Sets validation groups
     *
     * @param array|null $validationGroups
     * @return $this
     */
    public function setValidationGroups(array $validationGroups = null)
    {
        $this->validationGroups = $validationGroups;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getEncoder()
    {
        return $this->encoder;
    }

    /**
     * Sets response encoder
     *
     * @param EncoderInterface $encoder
     * @return $this
     */
    public function setEncoder(EncoderInterface $encoder)
    {
        $this->encoder = $encoder;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getEncoderMediaType()
    {
        return $this->encoderMediaType;
    }

    /**
     * Sets response encoder media type
     *
     * @param MediaTypeInterface $encoderMediaType
     * @return $this
     */
    public function setEncoderMediaType(MediaTypeInterface $encoderMediaType)
    {
        $this->encoderMediaType = $encoderMediaType;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setDecoder(DecoderInterface $decoder)
    {
        $this->decoder = $decoder;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDecoder()
    {
        return $this->decoder;
    }

    /**
     * Sets environment configuration from array
     *
     * @param array $config
     */
    protected function fromArray(array $config)
    {
        $fields = [
            'query' => 'setQueryType',
            'body' => 'setBodyType',
            'matcher' => 'setMatcherConfiguration',
            'urlPrefix' => 'setUrlPrefix',
            'validation' => 'setValidationGroups'
        ];

        foreach ($fields as $field => $setter) {
            if (!array_key_exists($field, $config)) {
                continue;
            }

            $this->{$setter}($config[$field]);
        }
    }
}
