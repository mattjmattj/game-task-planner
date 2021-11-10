<?php

namespace App\Backtracking\Constraint;

use App\Backtracking\BacktrackableAssignment;
use App\Entity\Person;
use App\Entity\TaskType;

/**
 * Constraint forcing a given assignment of a task
 */
final class ForcedTaskConstraint implements ConstraintInterface
{
    public function __construct(
        private int $game,
        private TaskType $type,
        private Person $person
    )
    {}

    public function reject(BacktrackableAssignment $assignment): bool
    {
        $taskSlots = $assignment->getTaskSlots();
        $value = $taskSlots[$this->game][$this->type];
        return 
            $value !== false // not assigned yet is ok
            && $value !== $this->person;
    }

    public function validate(BacktrackableAssignment $assignment): bool
    {
        $taskSlots = $assignment->getTaskSlots();
        return $taskSlots[$this->game][$this->type] === $this->person;
    }
}