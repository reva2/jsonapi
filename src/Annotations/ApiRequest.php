<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) OrbitScripts LLC <support@orbitscripts.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Annotations;

/**
 * JSON API request annotation
 *
 * @package Reva2\JsonApi\Annotations
 * @author Sergey Revenko <reva2@orbita1.ru>
 *
 * @Annotation
 * @Target({"CLASS","METHOD"})
 */
class ApiRequest
{
    /**
     * Query type
     *
     * @var string
     */
    public $query;

    /**
     * Body type
     *
     * @var string
     */
    public $body;

    /**
     * Code matcher
     *
     * @var Reva2\JsonApi\Annotations\Matcher
     */
    public $matcher;

    /**
     * Prefix for URLs generator
     *
     * @var string
     */
    public $urlPrefix;

    /**
     * @return array
     */
    public function toArray()
    {
        $data = [];

        $properties = ['query', 'body', 'urlPrefix'];
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