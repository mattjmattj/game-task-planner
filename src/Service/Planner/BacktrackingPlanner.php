<?php

namespace App\Service\Planner;

use App\Assignment\Assignment;
use App\Entity\Person;
use App\Entity\Planning;
use App\Entity\TaskType;
use App\Backtracking\BacktrackableAssignment;
use App\Backtracking\Constraint\ConstraintInterface;
use App\Backtracking\Constraint\RejectableConstraintInterface;
use App\Backtracking\Heuristic\LesserTasksPersonChooserHeuristic;
use App\Backtracking\Heuristic\PersonChooserHeuristicInterface;

final class BacktrackingPlanner implements PlannerInterface
{
    /** @var ConstraintInterface[] */
    private array $constraints = [];

    private int $backtrackingCalls = 0;

    private int $maxBacktracking = 0;

    private PersonChooserHeuristicInterface $personChooserHeuristic;

    public function __construct()
    {
        $this->personChooserHeuristic = new LesserTasksPersonChooserHeuristic;
    }

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

    public function getMaxBacktracking(): int
    {
        return $this->maxBacktracking;
    }

    public function setMaxBacktracking(int $maxBacktracking): self
    {
        $this->maxBacktracking = $maxBacktracking;
        return $this;
    }

    public function setPersonChooserHeuristic(PersonChooserHeuristicInterface $personChooserHeuristic): self
    {
        $this->personChooserHeuristic = $personChooserHeuristic;
        return $this;
    }

    public function makeAssignment(Planning $planning): Assignment
    {
        $assignment = $this->backtrack(new BacktrackableAssignment($planning));
        if (!$assignment) {
            throw new ImpossiblePlanningException('Impossible to satisfy the given set of constraints');
        }
        return $assignment;
    }

    private function validate(BacktrackableAssignment $assignment): bool
    {
        foreach ($this->constraints as $constraint) {
            if (!$constraint->validate($assignment)) {
                return false;
            }
        }
        return true;
    }

    private function reject(BacktrackableAssignment $assignment): bool
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
        $heuristic = $this->personChooserHeuristic;
        yield from $heuristic($ba, $game, $type);
    }

    private function backtrack(BacktrackableAssignment $ba): ?Assignment
    {
        $this->backtrackingCalls++;

        if ($this->maxBacktracking > 0 && $this->backtrackingCalls > $this->maxBacktracking) {
            throw new MaximumBacktrackingException('Maximum backtracking of ' . $this->maxBacktracking . ' reached');
        }

        if ($this->validate($ba)) {
            return $ba->makeAssignment();
        }

        if ($this->reject($ba)) {
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

    public function getBacktrackingCalls(): int
    {
        return $this->backtrackingCalls;
    }
}