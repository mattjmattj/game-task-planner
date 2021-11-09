<?php

namespace App\Backtracking\Constraint;

use App\Backtracking\BacktrackableAssignment;

/**
 * Constraint preventing someone from being given the same task type more than anyone else
 */
final class NoSpecialistConstraint implements ConstraintInterface
{
    public function validate(BacktrackableAssignment $assignment): bool
    {
        $taskTypes = $assignment->getTaskSlotsPerType();

        foreach ($taskTypes as $type) {
            $counts = new \SplObjectStorage;
            foreach ($taskTypes[$type] as $person) {
                if (false === $person) {
                    continue;
                }
                $counts[$person] = $counts->contains($person)
                    ? $counts[$person] + 1
                    : 1;
            }

            $min = PHP_INT_MAX;
            $max = 0;
            foreach ($counts as $person) {
                $n = $counts[$person];
                if ($n < $min) {
                    $min = $n;
                }
                if ($n > $max) {
                    $max = $n;
                }
            }

            if ($counts->count() !== $assignment->getPlanning()->getPersons()->count()) {
                // some people are missing : min = 0
                $min = 0;
            }

            if ($max - $min > 1 ) {
                return false;
            }
        }

        return true;
    }
}