<?php

namespace App\Service\Planner;

use App\Entity\Assignment;
use App\Entity\Person;
use App\Entity\Planning;
use App\Entity\TaskType;
use App\Service\AssignmentGenerator;
use App\Service\Planner\Constraint\BacktrackableAssignment;
use App\Service\Planner\Constraint\ConstraintInterface;
use App\Service\Planner\Constraint\RejectableConstraintInterface;

final class ConstraintBasedPlanner implements PlannerInterface
{
    /** @var ConstraintInterface[] */
    private array $constraints = [];

    private int $backtrackingCalls = 0;

    public function __construct(
        private AssignmentGenerator $assignmentGenerator
    )
    {}

    public function setConstraints(array $constraints): self
    {
        $this->constraints = $constraints;
        return $this;
    }

    public function addConstraint(ConstraintInterface $constraint): self
    {
        $this->constraints[] = $constraint;
        return $this;
    }

    public function makeAssignment(Planning $planning): Assignment
    {
        $assignment = $this->backtrack(new BacktrackableAssignment($planning));
        if (!$assignment) {
            throw new ImpossiblePlanningException('Impossible to satisfy the given set of constraints');
        }
        var_dump($this->backtrackingCalls);
        return $assignment;
    }

    private function validate(Assignment $assignment): bool
    {
        foreach ($this->constraints as $constraint) {
            if (!$constraint->validate($assignment)) {
                return false;
            }
        }
        return true;
    }

    private function reject(Assignment $assignment): bool
    {
        foreach ($this->constraints as $constraint) {
            if ($constraint instanceof RejectableConstraintInterface
             && $constraint->reject($assignment)) {
                return true;
            }
        }
        return false;
    }

    private function pickTaskSlot(BacktrackableAssignment $ba): array
    {
        // TODO we can do better
        $availableTaskSlots = $ba->getAvailableTaskSlots();
        if (empty($availableTaskSlots)) {
            throw new \Exception("No slot available");
        }
        return array_pop($availableTaskSlots);
    }

    private function choosePerson(BacktrackableAssignment $ba, int $game, TaskType $type): iterable
    {
        $persons = $ba->getAvailablePersons($game, $type);
        usort($persons, fn(Person $a, Person $b) => $ba->getTaskCount($a) <=> $ba->getTaskCount($b));
        foreach ($persons as $person) {
            yield $person;
        }
    }

    private function backtrack(BacktrackableAssignment $ba): ?Assignment
    {
        $this->backtrackingCalls++;

        $assignment = $ba->makeAssignment();
        if ($this->validate($assignment)) {
            return $assignment;
        }

        if ($this->reject($assignment)) {
            return null;
        }

        try {
            [$game, $type] = $this->pickTaskSlot($ba);
        } catch (\Throwable) {
            return null;
        }
        foreach ($this->choosePerson($ba, $game, $type) as $person) {
            $ba->setTask($game, $type, $person);
            $assignment = $this->backtrack($ba);
            if ($assignment) {
                return $assignment;
            }
            $ba->unsetTask($game, $type);
        }
        return null;
    }

    public function getBacktrackingCall(): int
    {
        return $this->backtrackingCalls;
    }
}