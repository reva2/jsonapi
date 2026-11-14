<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reva2\JsonApi\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class Request
{
    /**
     * Query type
     */
    public ?string $query;

    /**
     * Body type
     */
    public ?string $body;

    /**
     * Is body optional (request without body is accepted). Honoured only by the bundled
     * Reva2\JsonApi\Services\Environment: other EnvironmentInterface implementations ignore it, so an empty request
     * is rejected with 415 as for a required body
     *
     * @var ?bool
     */
    public ?bool $bodyOptional;

    /**
     * Code matcher
     *
     * @var ?Matcher
     */
    public ?Matcher $matcher;

    /**
     * Prefix for URLs generator
     *
     * @var ?string
     */
    public ?string $urlPrefix;

    /**
     * List of serialization groups
     *
     * @var string[]
     */
    public array $serialization;

    /**
     * List of validation groups that should be
     * automatically checked on request parsing
     *
     * @var string[]
     */
    public array $validation;

    /**
     * @param string|null $query
     * @param string|null $body
     * @param Matcher $matcher
     * @param string|null $urlPrefix
     * @param array|string[] $serialization
     * @param string[] $validation
     * @param bool|null $bodyOptional
     */
    public function __construct(
        ?string  $query = null,
        ?string  $body = null,
        ?string  $urlPrefix = null,
        ?Matcher $matcher = null,
        array    $serialization = ['Default'],
        array    $validation = ['Default'],
        ?bool    $bodyOptional = null,
    )
    {
        $this->query = $query;
        $this->body = $body;
        $this->matcher = $matcher;
        $this->urlPrefix = $urlPrefix;
        $this->serialization = $serialization;
        $this->validation = $validation;
        $this->bodyOptional = $bodyOptional;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $data = [];

        $properties = ['query', 'body', 'urlPrefix', 'validation', 'serialization', 'bodyOptional'];
        foreach ($properties as $property) {
            $value = $this->{$property};
            if (null !== $value) {
                $data[$property] = $value;
            }
        }

        if (null !== $this->matcher) {
            $data['matcher'] = $this->matcher->toArray();
        }

        return $data;
    }
}