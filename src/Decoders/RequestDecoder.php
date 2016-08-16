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

use Neomerx\JsonApi\Document\Error;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Reva2\JsonApi\Contracts\Decoders\DataParserInterface;
use Reva2\JsonApi\Contracts\Decoders\DecoderInterface;

/**
 * JSON API request decoder
 *
 * @package Reva2\JsonApi\Decoders
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class RequestDecoder implements DecoderInterface
{
    const INVALID_JSON_ERROR = 'add83393-3bdc-4042-a9c8-ecd5415e1a04';
    
    /**
     * ApiDocument parser
     *
     * @var DataParserInterface
     */
    protected $parser;

    /**
     * Expected document type
     *
     * @var string
     */
    protected $contentType;

    /**
     * Constructor
     *
     * @param DataParserInterface $parser
     */
    public function __construct(DataParserInterface $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @inheritdoc
     */
    public function setContentType($type)
    {
        $this->contentType = $type;
    }

    /**
     * @inheritdoc
     */
    public function decode($content)
    {
        $data = $this->decodeJson($content);

        return (null !== $this->contentType) ? $this->parser->parseDocument($data, $this->contentType) : $data;
    }

    /**
     * Decode JSON string to object
     *
     * @param string $content
     * @return object
     */
    protected function decodeJson($content)
    {
        $jsonErrors = array(
            JSON_ERROR_DEPTH => 'JSON_ERROR_DEPTH - Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH => 'JSON_ERROR_STATE_MISMATCH - Underflow or the modes mismatch',
            JSON_ERROR_CTRL_CHAR => 'JSON_ERROR_CTRL_CHAR - Unexpected control character found',
            JSON_ERROR_SYNTAX => 'JSON_ERROR_SYNTAX - Syntax error, malformed JSON',
            JSON_ERROR_UTF8 => 'JSON_ERROR_UTF8 - Malformed UTF-8 characters, possibly incorrectly encoded'
        );

        // Can we use JSON_BIGINT_AS_STRING?
        $options = 0;
        if (version_compare(PHP_VERSION, '5.4.0', '>=') && !(defined('JSON_C_VERSION') && PHP_INT_SIZE > 4)) {
            $options = JSON_BIGINT_AS_STRING;
        };

        $data = json_decode($content, false, 512, $options);
        if (JSON_ERROR_NONE !== json_last_error()) {
            $last = json_last_error();
            $error = 'Unknown error';

            if (isset($jsonErrors[$last])) {
                $error = $jsonErrors[$last];
            }
            
            $apiError = new Error(
                rand(),
                null,
                400,
                self::INVALID_JSON_ERROR,
                'Unable to parse JSON data',
                $error
            );

            throw new JsonApiException($apiError, 400);
        }

        return $data;
    }
}
