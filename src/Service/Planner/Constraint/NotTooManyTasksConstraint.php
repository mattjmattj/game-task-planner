<?php

namespace App\Service\Planner\Constraint;

use App\Entity\Assignment;
use SplObjectStorage;

final class NotTooManyTasksConstraint implements ConstraintInterface
{
    public function validate(Assignment $assignment): bool
    {
        $tasksPerPerson = new SplObjectStorage;
        foreach ($assignment->getPlanning()->getPersons() as $person) {
            $tasksPerPerson[$person] = 0;
        }

        foreach ($assignment->getTasks() as $task) {
            /** @var Task $task */
            $tasksPerPerson[$task->getAssignee()] = $tasksPerPerson[$task->getAssignee()] + 1;
        }

        $min = PHP_INT_MAX;
        $max = 0;
        foreach ($tasksPerPerson as $person) {
            $n = $tasksPerPerson[$person];
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