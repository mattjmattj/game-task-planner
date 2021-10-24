<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class Assignement extends Constraint
{
    public string $message = 'The assignement is not complete';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
