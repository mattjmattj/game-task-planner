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
        $repartition = new \SplObjectStorage;
        foreach ($assignment->getPlanning()->getTaskTypes() as $type) {
            $repartition[$type] = new \SplObjectStorage;
            foreach ($assignment->getPlanning()->getPersons() as $person) {
                $repartition[$type][$person] = 0;
            }
        }

        foreach ($assignment->getTaskSlots() as $gameTaskSlots) {
            foreach ($gameTaskSlots as $type) {
                $person = $gameTaskSlots[$type];
                if (!$person) {
                    continue;
                }
                $repartition[$type][$person] = $repartition[$type][$person] + 1;
            }
        }

        foreach ($repartition as $type) {
            $min = PHP_INT_MAX;
            $max = 0;
            foreach ($repartition[$type] as $person) {
                $n = $repartition[$type][$person];
                if ($n < $min) {
                    $min = $n;
                }
                if ($n > $min) {
                    $max = $n;
                }
            }
            if ($max - $min > 1) {
                return false;
            }
        }

        return true;
    }
}