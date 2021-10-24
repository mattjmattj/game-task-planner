<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AssignementValidator extends ConstraintValidator
{
    
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Assignement) {
            return;
        }

        if (null === $value || '' === $value) {
            return;
        }

        // TODO: implement the validation here
        // $this->context->buildViolation($constraint->message)
        //     ->setParameter('{{ value }}', $value)
        //     ->addViolation();
    }
}
