<?php

namespace App\Service\Planner\Constraint;

use App\Entity\Assignment;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class AssignmentValidatorConstraint implements ConstraintInterface
{
    public function __construct(
        private ValidatorInterface $validator
    )
    {}

    public function validate(Assignment $assignment): bool
    {
        return $this->validator->validate($assignment)->count() === 0;
    }
}