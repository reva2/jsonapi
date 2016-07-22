<?php
/*
 * This file is part of the reva2/jsonapi.
 *
 * (c) OrbitScripts LLC <support@orbitscripts.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Reva2\JsonApi\Http\Query;

use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * JSON API single resource query parameters
 *
 * @package Reva2\JsonApi\Http\Query
 * @author Sergey Revenko <reva2@orbita1.ru>
 *
 * @Reva2\JsonApi\Annotations\ApiObject()
 */
class QueryParameters implements EncodingParametersInterface
{
    const INVALID_INCLUDE_PATHS = '9f4922b8-8e8b-4847-baf2-5831adfd6813';
    const INVALID_FIELD_SET = 'ec7d2c6b-97d1-4f94-ba94-d141d985fc6f';

    /**
     * @var string[]|null
     * @Reva2\JsonApi\Annotations\Property(path="[include]", parser="parseIncludePaths")
     * @Symfony\Component\Validator\Constraints\Type(type="array")
     * @Symfony\Component\Validator\Constraints\All({
     *     @Symfony\Component\Validator\Constraints\Type(type="string")
     * })
     */
    protected $includePaths;

    /**
     * @var array[]|null
     * @Reva2\JsonApi\Annotations\Property(path="[fields]", parser="parseFieldSets")
     * @Symfony\Component\Validator\Constraints\Type(type="array")
     * @Symfony\Component\Validator\Constraints\All({
     *     @Symfony\Component\Validator\Constraints\Type(type="string")
     * })
     */
    protected $fieldSets;

    /**
     * @inheritdoc
     */
    public function getIncludePaths()
    {
        return $this->includePaths;
    }

    /**
     * Sets include paths
     *
     * @param string[]|null $paths
     * @return $this
     */
    public function setIncludePaths(array $paths = null)
    {
        $this->includePaths = $paths;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getFieldSets()
    {
        return $this->fieldSets;
    }

    /**
     * @inheritdoc
     */
    public function getFieldSet($type)
    {
        return (isset($this->fieldSets[$type])) ? $this->fieldSets[$type] : null;
    }

    /**
     * @param \array[]|null $fieldSets
     * @return $this
     */
    public function setFieldSets(array $fieldSets = null)
    {
        $this->fieldSets = $fieldSets;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSortParameters()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getPaginationParameters()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getFilteringParameters()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getUnrecognizedParameters()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function isEmpty()
    {
        if (empty($this->getIncludePaths()) &&
            empty($this->getFieldSets()) &&
            empty($this->getSortParameters()) &&
            empty($this->getPaginationParameters()) &&
            empty($this->getFilteringParameters())
        ) {
            return true;
        }

        return false;
    }

    /**
     * Parse value of parameter that store include paths
     * which should be included into response
     *
     * @param string|null $value
     * @return array|null
     */
    public function parseIncludePaths($value = null) {
        if (empty($value)) {
            return null;
        }

        if (!is_string($value)) {
            throw new \InvalidArgumentException('Include paths value must be a string', 400);
        }

        return explode(',', $value);
    }

    /**
     * Parse value of parameter that store fields which
     * should be included into response
     *
     * @param array|null $value
     * @return array|null
     */
    public function parseFieldSets($value = null) {
        if (empty($value)) {
            return null;
        }

        if (!is_array($value)) {
            throw new \InvalidArgumentException('Field sets value must be an array', 400);
        }

        foreach ($value as $resource => $fields) {
            $value[$resource] = explode(',', $fields);
        }

        return $value;
    }

    /**
     * Validate specified include paths
     *
     * @param ExecutionContextInterface $context
     * @Symfony\Component\Validator\Constraints\Callback()
     */
    public function validateIncludePaths(ExecutionContextInterface $context)
    {
        if (!is_array($this->includePaths)) {
            return;
        }

        $invalidPaths = array_diff($this->includePaths, $this->getAllowedIncludePaths());
        if (count($invalidPaths) > 0) {
            $context
                ->buildViolation('Invalid include paths: %paths%')
                ->setParameter('%paths%', sprintf("'%s'", implode("', '", $invalidPaths)))
                ->setPlural(count($invalidPaths))
                ->setInvalidValue($invalidPaths)
                ->setCode(self::INVALID_INCLUDE_PATHS)
                ->atPath('includePaths')
                ->addViolation();
        }
    }

    /**
     * Validate specified fields sets
     *
     * @param ExecutionContextInterface $context
     * @Symfony\Component\Validator\Constraints\Callback()
     */
    public function validateFieldSets(ExecutionContextInterface $context)
    {
        if (!is_array($this->fieldSets)) {
            return;
        }

        foreach ($this->fieldSets as $resource => $fields) {
            $invalidFields = array_diff($fields, $this->getAllowedFields($resource));

            if (count($invalidFields) > 0) {
                $context
                    ->buildViolation('Invalid fields: %fields%')
                    ->setParameter('%fields%', sprintf("'%s'", implode("', '", $invalidFields)))
                    ->setPlural(count($invalidFields))
                    ->setInvalidValue($invalidFields)
                    ->setCode(self::INVALID_FIELD_SET)
                    ->atPath('fieldSets.' . $resource)
                    ->addViolation();
            }
        }
    }

    /**
     * Returns list of allowed include paths
     *
     * @return string[]
     */
    protected function getAllowedIncludePaths()
    {
        return [];
    }

    /**
     * Returns list of fields available in specified resource
     *
     * @param string $resource
     * @return array[]
     */
    protected function getAllowedFields($resource)
    {
        return [];
    }
}
