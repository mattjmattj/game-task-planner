<?php

namespace App\Backtracking\Constraint;

use App\Backtracking\BacktrackableAssignment;
use App\Entity\Person;

/**
 * Constraint reprensenting the fact taht a given person cannot perform any task
 * for a particular game
 */
final class UnavailablePersonConstraint implements RejectableConstraintInterface
{
    public function __construct(
        private Person $person,
        private int $game
    )
    {}
    
    public function validate(BacktrackableAssignment $assignment): bool
    {
        return !$this->reject($assignment);
    }

    public function reject(BacktrackableAssignment $assignment): bool
    {
        $taskSlots = $assignment->getTaskSlots();
        foreach ($taskSlots[$this->game] as $type) {
            if ($this->person === $taskSlots[$this->game][$type]) {
                return true;
            }
        }
        return false;
    }
}