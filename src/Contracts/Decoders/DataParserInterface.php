<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) Sergey Revenko <dedsemen@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Reva2\JsonApi\Contracts\Decoders;

use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;

/**
 * Interface for JSON API requests parser
 *
 * @package Reva2\JsonApi\Contracts\Decoders
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
interface DataParserInterface
{
    /**
     * Sets current path
     *
     * @param string $path
     * @return $this
     */
    public function setPath($path);

    /**
     * Restore path to previous step
     *
     * @return $this
     */
    public function restorePath();

    /**
     * Returns current path
     *
     * @return string
     */
    public function getPath();

    /**
     * Returns true if data object has value at specified
     * path. False otherwise.
     *
     * @param object|array $data
     * @param string $path
     * @return bool
     */
    public function hasValue($data, $path);

    /**
     * Returns data object value at specified path
     *
     * @param object|array $data
     * @param string $path
     * @return mixed
     */
    public function getValue($data, $path);

    /**
     * Parse data object value at specified path as is
     *
     * @param object|array $data
     * @param string $path
     * @return mixed
     */
    public function parseRaw($data, $path);

    /**
     * Parse data object value at specified path using callback
     *
     * @param object|array $data
     * @param string $path
     * @param callable $callback
     * @return mixed
     */
    public function parseCallback($data, $path, $callback);

    /**
     * Parse data object value at specified path as string
     *
     * @param object|array $data
     * @param string $path
     * @return string|null
     * @throws \InvalidArgumentException
     */
    public function parseString($data, $path);

    /**
     * Parse data object value at specified path as integer
     *
     * @param object|array $data
     * @param string $path
     * @return int|null
     * @throws \InvalidArgumentException
     */
    public function parseInt($data, $path);

    /**
     * Parse data object value at specified path as float
     *
     * @param object|array $data
     * @param string $path
     * @return float|null
     * @throws \InvalidArgumentException
     */
    public function parseFloat($data, $path);

    /**
     * Parse data object value at specified path as boolean
     *
     * @param object|array $data
     * @param string $path
     * @return bool|null
     * @throws \InvalidArgumentException
     */
    public function parseBool($data, $path);

    /**
     * Parse data object value at specified path as date/time object
     *
     * @param object|array $data
     * @param string $path
     * @param string $format
     * @return \DateTimeImmutable|null
     * @throws \InvalidArgumentException
     */
    public function parseDateTime($data, $path, $format);

    /**
     * Parse data object value at specified path as array
     *
     * @param object|array $data
     * @param string $path
     * @param \Closure $itemsParser
     * @return array
     * @throws \InvalidArgumentException
     */
    public function parseArray($data, $path, \Closure $itemsParser);

    /**
     * Parse data object value at specified path as resource
     * of specified type
     *
     * @param object|array $data
     * @param string $path
     * @param string $resType
     * @return mixed|null
     */
    public function parseResource($data, $path, $resType);

    /**
     * Parse data object as JSON API object
     *
     * @param object|array $data
     * @param string $objType
     * @return mixed
     */
    public function parseObject($data, $path, $objType);

    /**
     * Parse data object as JSON API document
     *
     * @param object|array $data
     * @param string $docType
     * @return mixed
     */
    public function parseDocument($data, $docType);

    /**
     * @param object|array $data
     * @param string $paramsType
     * @return EncodingParametersInterface
     */
    public function parseQueryParams($data, $paramsType);
}
