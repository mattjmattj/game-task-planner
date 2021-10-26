<?php

namespace App\Service\Planner\Constraint;

use App\Entity\Assignement;

interface ConstraintInterface
{
    public function validate(Assignement $assignement): bool;
}