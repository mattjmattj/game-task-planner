<?php

namespace App\Backtracking\Constraint;

use App\Backtracking\BacktrackableAssignment;

interface RejectableConstraintInterface extends ConstraintInterface
{
    /**
     * Must return true if, and only if, there is no way to **add** Tasks to 
     * the provided BacktrackableAssignment that would make it validate the constraint
     */
    public function reject(BacktrackableAssignment $assignment): bool;
}