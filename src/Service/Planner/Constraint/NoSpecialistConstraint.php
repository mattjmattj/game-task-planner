<?php

namespace App\Service\Planner\Constraint;

use App\Entity\Assignment;
use SplObjectStorage;

/**
 * Constraint preventing someone from being given the same task type more than anyone else
 */
final class NoSpecialistConstraint implements ConstraintInterface
{
    public function validate(Assignment $assignment): bool
    {
        $repartition = new \SplObjectStorage;
        foreach ($assignment->getPlanning()->getTaskTypes() as $type) {
            $repartition[$type] = new \SplObjectStorage;
            foreach ($assignment->getPlanning()->getPersons() as $person) {
                $repartition[$type][$person] = 0;
            }
        }

        foreach ($assignment->getTasks() as $task) {
            $t = $task->getType();
            $p = $task->getAssignee();
            $repartition[$t][$p] = $repartition[$t][$p] + 1;
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