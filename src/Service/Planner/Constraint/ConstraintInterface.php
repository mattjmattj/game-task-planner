<?php

namespace App\Service\Planner\Constraint;

interface ConstraintInterface
{
    public function validate(BacktrackableAssignment $assignment): bool;
}