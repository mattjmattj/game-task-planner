<?php

namespace App\Backtracking\Constraint;

use App\Backtracking\BacktrackableAssignment;

interface ConstraintInterface
{
    public function validate(BacktrackableAssignment $assignment): bool;
}