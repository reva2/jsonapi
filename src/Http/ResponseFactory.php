<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reva2\JsonApi\Http;

use Neomerx\JsonApi\Contracts\Encoder\EncoderInterface;
use Neomerx\JsonApi\Contracts\Http\Headers\MediaTypeInterface;
use Neomerx\JsonApi\Contracts\Schema\SchemaContainerInterface;
use Neomerx\JsonApi\Http\BaseResponses;
use Reva2\JsonApi\Contracts\Encoder\EncodingParametersInterface;
use Reva2\JsonApi\Contracts\Services\EnvironmentInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * JSON API response factory
 *
 * @package Reva2\JsonApi\Http
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class ResponseFactory extends BaseResponses
{
    const PARAM_SUPPORTED_EXT = 'supported-ext';
    const PARAM_EXT = 'ext';

    const HEADER_LOCATION = 'Location';

    /**
     * @var SchemaContainerInterface
     */
    protected SchemaContainerInterface $schemas;

    /**
     * @var EnvironmentInterface
     */
    protected EnvironmentInterface $environment;

    /**
     * @var EncodingParametersInterface|null
     */
    protected ?EncodingParametersInterface $params;

    /**
     * Constructor
     *
     * @param SchemaContainerInterface $schemas
     * @param EnvironmentInterface $environment
     * @param EncodingParametersInterface|null $params
     */
    public function __construct(
        SchemaContainerInterface $schemas,
        EnvironmentInterface $environment,
        EncodingParametersInterface $params = null
    ) {
        $this->schemas = $schemas;
        $this->environment = $environment;
        $this->params = $params;
    }

    public function buildContentResponse(
        mixed $data,
        int $statusCode = self::HTTP_OK,
        mixed $meta = null,
        array $links = [],
        array $headers = []
    ): Response {
        $encoder = $this->getEncoder();
        $this->addMetadataAndLinks($encoder, $meta, $links);

        $content = $encoder->encodeData($data);

        return $this->createJsonApiResponse($content, $statusCode, $headers);
    }

    public function buildCreatedResponse(
        mixed $data,
        mixed $meta = null,
        array $links = [],
        array $headers = []
    ): Response
    {
        return $this->buildContentResponse($data, Response::HTTP_CREATED, $meta, $links, $headers);
    }

    public function buildEmptyResponse(array $headers = []): Response
    {
        return $this->createJsonApiResponse(null, Response::HTTP_NO_CONTENT, $headers);
    }

    protected function createJsonApiResponse(
        ?string $content,
        ?int $statusCode = Response::HTTP_OK,
        array $headers = [],
        $addContentType = true
    ): Response {
        if ($addContentType === true) {
            $mediaType   = $this->getMediaType();
            $contentType = $mediaType->getMediaType();
            $params      = $mediaType->getParameters();

            $separator = ';';
            if (isset($params[self::PARAM_EXT])) {
                $ext = $params[self::PARAM_EXT];
                if (empty($ext) === false) {
                    $contentType .= $separator . self::PARAM_EXT . '="' . $ext . '"';
                    $separator   = ',';
                }
            }

            $extensions = $this->getSupportedExtensions();
            if ($extensions !== null && ($list = $extensions->getExtensions()) !== null && empty($list) === false) {
                $contentType .= $separator . self::PARAM_SUPPORTED_EXT . '="' . $list . '"';
            }

            $headers['Content-Type'] = $contentType;
        }

        return $this->createResponse($content, $statusCode, $headers);
    }

    /**
     * @param string|null $content
     * @param int $statusCode
     * @param array $headers
     * @return Response
     */
    protected function createResponse(?string $content = null, int $statusCode = 200, array $headers = []): Response
    {
        return new Response($content, $statusCode, $headers);
    }


    protected function getEncoder(): EncoderInterface
    {
        $encoder = $this->environment->getEncoder();
        $encoder
            ->reset()
            ->withUrlPrefix($this->environment->getUrlPrefix());

        if ($this->params !== null) {
            $encoder
                ->withFieldSets($this->params->getFieldSets())
                ->withIncludedPaths($this->params->getIncludePaths());
        }

        return $this->environment->getEncoder();
    }

    /**
     * @return EncodingParametersInterface
     */
    protected function getEncodingParameters(): EncodingParametersInterface
    {
        return $this->params;
    }

    /**
     * @return SchemaContainerInterface
     */
    protected function getSchemaContainer(): SchemaContainerInterface
    {
        return $this->schemas;
    }

    /**
     * @return array|null
     */
    protected function getSupportedExtensions(): ?array
    {
        return null;
    }

    /**
     * @return MediaTypeInterface
     */
    protected function getMediaType(): MediaTypeInterface
    {
        return $this->environment->getEncoderMediaType();
    }

    private function addMetadataAndLinks(EncoderInterface $encoder, mixed $meta = null, array $links = []): void
    {
        if (!empty($meta)) {
            $encoder->withMeta($meta);
        }

        if (!empty($links)) {
            $encoder->withLinks($links);
        }
    }
}
