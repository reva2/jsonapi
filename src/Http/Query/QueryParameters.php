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
use Reva2\JsonApi\Annotations as API;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * JSON API single resource query parameters
 *
 * @package Reva2\JsonApi\Http\Query
 * @author Sergey Revenko <reva2@orbita1.ru>
 */
class QueryParameters implements EncodingParametersInterface
{
    const INVALID_INCLUDE_PATHS = '9f4922b8-8e8b-4847-baf2-5831adfd6813';
    const INVALID_FIELD_SET = 'ec7d2c6b-97d1-4f94-ba94-d141d985fc6f';

    /**
     * @var string[]|null
     * @API\Property(path="[include]", parser="parseIncludePaths")
     * @Assert\Type(type="array")
     * @Assert\All({@Assert\Type(type="string")})
     */
    protected $includePaths;

    /**
     * @var array[]|null
     * @API\Property(path="[fields]", parser="parseFieldSets")
     * @Assert\Type(type="array")
     * @Assert\All({@Assert\Type(type="string")})
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
     * @param array[]|null $fieldSets
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
     * @Assert\Callback()
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
     * @Assert\Callback()
     */
    public function validateFieldSets(ExecutionContextInterface $context)
    {
        if (!is_array($this->fieldSets)) {
            return;
        }

        foreach ($this->fieldSets as $resource => $fields) {
            $invalidFields = array_diff($fields, $this->getAllowedFields($resource));

            if (count($invalidFields) > 0) {
                $this->addViolation(
                    $context,
                    'Invalid fields: %fields%',
                    ['%fields%' => sprintf("'%s'", implode("', '", $invalidFields))],
                    $invalidFields,
                    self::INVALID_FIELD_SET,
                    'fieldSets.' . $resource,
                    count($invalidFields)
                );
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
        switch ($resource) {
            default:
                return [];
        }
    }

    /**
     * Add specified violation
     *
     * @param ExecutionContextInterface $context
     * @param string $message
     * @param array $params
     * @param int $plural
     * @param mixed $invalidValue
     * @param string $code
     * @param string $path
     */
    protected function addViolation(
        ExecutionContextInterface $context,
        $message,
        array $params,
        $invalidValue,
        $code,
        $path,
        $plural = null
    ) {
        $builder = $context
            ->buildViolation($message)
            ->setParameters($params)
            ->setInvalidValue($invalidValue)
            ->setCode($code)
            ->atPath($path);

        if (null !== $plural) {
            $builder->setPlural($plural);
        }

        $builder->addViolation();
    }
}
