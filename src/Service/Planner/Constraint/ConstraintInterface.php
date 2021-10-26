<?php

namespace App\Service\Planner\Constraint;

use App\Entity\Assignment;

interface ConstraintInterface
{
    public function validate(Assignment $assignment): bool;
}