<?php

namespace App\Application\Handler;

use Symfony\Component\Validator\ConstraintViolationListInterface;

abstract class BaseEntityManger
{
    protected  function getViolations(ConstraintViolationListInterface $violations): array
    {
        $violationsErrors = [];
        if (count($violations) > 0) {
            foreach ($violations as $violation) {
                $violationsErrors[$violation->getPropertyPath()] = $violation->getMessage();
            }
        }

        return $violationsErrors;
    }
}
