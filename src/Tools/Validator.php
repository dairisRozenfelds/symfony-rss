<?php

namespace App\Tools;

use BadMethodCallException;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Validator
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param array $validationMap
     * @param array $data
     * @return array
     */
    public function validate(array $validationMap, array $data): array
    {
        $errors = [];

        foreach ($validationMap as $field => $validators) {
            if (!array_key_exists($field, $data)) {
                throw new BadMethodCallException($field . ' is defined in the validation map but is missing in the data');
            }

            $result = $this->validator->validate(
                $data[$field],
                $validators
            );

            if (count($result) > 0) {
                /** @var ConstraintViolationInterface $error */
                foreach ($result as $error) {
                    $errors[$field][] = $error->getMessage();
                }
            }
        }

        return $errors;
    }
}
