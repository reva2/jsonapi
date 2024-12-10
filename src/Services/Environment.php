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
    protected ?string $queryType = null;

    /**
     * Expected body content type
     *
     * @var string|null
     */
    protected ?string $bodyType = null;

    /**
     * Codec matcher configuration
     *
     * @var array
     */
    protected array $matcherConfiguration;

    /**
     * URLs prefix
     *
     * @var string
     */
    protected string $urlPrefix = '';

    /**
     * Serialization groups
     *
     * @var string[]
     */
    protected array $serializationGroups = ['Default'];

    /**
     * Validation groups
     *
     * @var string[]|null
     */
    protected ?array $validationGroups = null;

    /**
     * Request decoder
     *
     * @var DecoderInterface|null
     */
    protected ?DecoderInterface $decoder;

    /**
     * Response encoder
     *
     * @var EncoderInterface|null
     */
    protected ?EncoderInterface $encoder;

    /**
     * Response encoder media type
     *
     * @var MediaTypeInterface|null
     */
    protected ?MediaTypeInterface $encoderMediaType;

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
    public function getQueryType(): ?string
    {
        return $this->queryType;
    }

    /**
     * Sets expected query parameters type
     *
     * @param null|string $queryType
     * @return $this
     */
    public function setQueryType(?string $queryType = null): self
    {
        $this->queryType = $queryType;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBodyType(): ?string
    {
        return $this->bodyType;
    }

    /**
     * Sets expected body content type
     *
     * @param null|string $bodyType
     * @return $this
     */
    public function setBodyType(?string $bodyType = null): self
    {
        $this->bodyType = $bodyType;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMatcherConfiguration(): array
    {
        return $this->matcherConfiguration;
    }

    /**
     * Sets codec matcher configuration
     *
     * @param array $matcherConfiguration
     * @return $this
     */
    public function setMatcherConfiguration(array $matcherConfiguration): self
    {
        $this->matcherConfiguration = $matcherConfiguration;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUrlPrefix(): string
    {
        return $this->urlPrefix;
    }

    /**
     * Sets URLs prefix
     *
     * @param string $urlPrefix
     * @return $this
     */
    public function setUrlPrefix(string $urlPrefix): self
    {
        $this->urlPrefix = $urlPrefix;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getSerializationGroups(): array
    {
        return $this->serializationGroups;
    }

    /**
     * @param string[] $serializationGroups
     * @return $this
     */
    public function setSerializationGroups(array $serializationGroups): self
    {
        $this->serializationGroups = $serializationGroups;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getValidationGroups(): ?array
    {
        return $this->validationGroups;
    }

    /**
     * Sets validation groups
     *
     * @param array|null $validationGroups
     * @return $this
     */
    public function setValidationGroups(array $validationGroups = null): self
    {
        $this->validationGroups = $validationGroups;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getEncoder(): ?EncoderInterface
    {
        return $this->encoder;
    }

    /**
     * Sets response encoder
     *
     * @param EncoderInterface|null $encoder
     * @return $this
     */
    public function setEncoder(?EncoderInterface $encoder): self
    {
        $this->encoder = $encoder;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getEncoderMediaType(): ?MediaTypeInterface
    {
        return $this->encoderMediaType;
    }

    /**
     * Sets response encoder media type
     *
     * @param MediaTypeInterface|null $mediaType
     * @return $this
     */
    public function setEncoderMediaType(?MediaTypeInterface $mediaType): self
    {
        $this->encoderMediaType = $mediaType;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setDecoder(?DecoderInterface $decoder): self
    {
        $this->decoder = $decoder;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDecoder(): ?DecoderInterface
    {
        return $this->decoder;
    }

    /**
     * Sets environment configuration from array
     *
     * @param array $config
     */
    protected function fromArray(array $config): void
    {
        $fields = [
            'query' => 'setQueryType',
            'body' => 'setBodyType',
            'matcher' => 'setMatcherConfiguration',
            'urlPrefix' => 'setUrlPrefix',
            'serialization' => 'setSerializationGroups',
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
