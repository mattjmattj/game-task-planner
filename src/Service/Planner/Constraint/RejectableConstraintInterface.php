<?php

namespace App\Service\Planner\Constraint;

use App\Entity\Assignment;

interface RejectableConstraintInterface extends ConstraintInterface
{
    /**
     * Must return true if, and only if, there is no way to **add** Tasks to 
     * the provided Assignement that would make it validate the constraint
     */
    public function reject(Assignment $assignment): bool;
}