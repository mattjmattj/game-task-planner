<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class Assignment extends Constraint
{
    public const MISSING_TASKS_ERROR = 100;
    public const DUPLICATED_TASKS_ERROR = 200;
    public const MULTIPLE_TASKS_ERROR = 300;

    public string $message = 'The assignment is not valid';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
