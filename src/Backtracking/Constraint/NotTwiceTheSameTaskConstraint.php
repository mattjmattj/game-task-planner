<?php

namespace App\Backtracking\Constraint;

use App\Backtracking\BacktrackableAssignment;

/**
 * Constraint preventing someone from being given the same task two games in a row
 */
final class NotTwiceTheSameTaskConstraint implements RejectableConstraintInterface
{
    public function validate(BacktrackableAssignment $assignment): bool
    {
        return !$this->reject($assignment);
    }

    public function reject(BacktrackableAssignment $assignment): bool
    {
        /** @var \SplObjectStorage */
        $taskSlots = $assignment->getTaskSlotsPerType();
        foreach ($taskSlots as $type) {
            $previous = null;
            foreach($taskSlots[$type] as $person) {
                if (false === $person) {
                    $previous = null;
                    continue;
                }
                if ($previous === $person) {
                    return true;
                }
                $previous = $person;
            }
        }
        return false;
    }
}