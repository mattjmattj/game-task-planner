<?php

namespace App\Service\Planner\Constraint;

final class NotTooManyTasksConstraint implements ConstraintInterface
{
    public function validate(BacktrackableAssignment $assignment): bool
    {
        $min = PHP_INT_MAX;
        $max = 0;
        foreach ($assignment->getPlanning()->getPersons() as $person) {
            $n = $assignment->getTaskCount($person);
            if ($n < $min) {
                $min = $n;
            }
            if ($n > $max) {
                $max = $n;
            }
        }

        return $max - $min <= 1;
    }
}