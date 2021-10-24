<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class Assignement extends Constraint
{
    public const MISSING_TASKS_ERROR = 100;
    public const DUPLICATED_TASKS_ERROR = 200;

    public string $message = 'The assignement is not complete';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
