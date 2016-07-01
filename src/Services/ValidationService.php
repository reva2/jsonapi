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

use Neomerx\JsonApi\Contracts\Encoder\Parameters\EncodingParametersInterface;
use Neomerx\JsonApi\Document\Error;
use Reva2\JsonApi\Contracts\Services\ValidationServiceInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * JSON API validation service that use symfony/validator component
 *
 * @package Reva2\JsonApi\Services
 * @author Sergey Revenko <dedsemen@gmail.com>
 */
class ValidationService implements ValidationServiceInterface
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * ValidationService constructor.
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @inheritdoc
     */
    public function validate($data, array $groups = null)
    {
        $errors = [];

        $violations = $this->validator->validate($data, null, $groups);
        foreach ($violations as $violation) {
            /* @var $violation \Symfony\Component\Validator\ConstraintViolationInterface */
            
            if ($data instanceof EncodingParametersInterface) {
                $source = ['parameter' => $violation->getPropertyPath()];
            } else {
                $source = ['pointer' => $this->prepareSourcePath($violation->getPropertyPath())];
            }
            
            $errors[] = new Error(
                rand(),
                null,
                422,
                $violation->getCode(),
                $violation->getMessage(),
                null,
                $source
            );
        }

        return $errors;
    }

    /**
     * @param string $path
     * @return string
     */
    private function prepareSourcePath($path)
    {
        return '/' . trim(preg_replace('~[\/]+~si', '/', str_replace(['.', '[', ']'], '/', (string) $path)), '/');
    }
}