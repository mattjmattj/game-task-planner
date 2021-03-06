<?php

namespace App\Backtracking\Constraint;

use App\Backtracking\BacktrackableAssignment;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class AssignmentValidatorConstraint implements ConstraintInterface
{
    public function __construct(
        private ValidatorInterface $validator
    )
    {}

    public function validate(BacktrackableAssignment $assignment): bool
    {
        return $this->validator->validate($assignment->makeAssignment())->count() === 0;
    }
}