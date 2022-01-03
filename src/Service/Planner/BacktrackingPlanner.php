<?php

namespace App\Service\Planner;

use App\Assignment\Assignment;
use App\Entity\Person;
use App\Entity\Planning;
use App\Entity\TaskType;
use App\Backtracking\BacktrackableAssignment;
use App\Backtracking\Constraint\ConstraintInterface;
use App\Backtracking\Constraint\RejectableConstraintInterface;
use App\Backtracking\DomainReducer\DomainReducerInterface;
use App\Backtracking\Heuristic\LesserTasksPersonChooserHeuristic;
use App\Backtracking\Heuristic\PersonChooserHeuristicInterface;
use App\Backtracking\Heuristic\NullTaskSlotChooserHeuristic;
use App\Backtracking\Heuristic\TaskSlotChooserHeuristicInterface;

final class BacktrackingPlanner implements PlannerInterface
{
    /** @var ConstraintInterface[] */
    private array $constraints = [];

    private int $backtrackingCalls = 0;

    private int $maxBacktracking = 0;

    private PersonChooserHeuristicInterface $personChooserHeuristic;

    private TaskSlotChooserHeuristicInterface $taskSlotChooserHeuristic;

    private array $domainReducers = [];

    public function __construct()
    {
        $this->personChooserHeuristic = new LesserTasksPersonChooserHeuristic;
        $this->taskSlotChooserHeuristic = new NullTaskSlotChooserHeuristic;
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

    public function setDomainReducers(array $domainReducers): self
    {
        $this->domainReducers = $domainReducers;
        return $this;
    }

    public function addDomainReducer(DomainReducerInterface $domainReducer): self
    {
        $this->domainReducers[] = $domainReducer;
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

    public function setTaskSlotChooserHeuristic(TaskSlotChooserHeuristicInterface $taskSlotChooserHeuristicInterface): self
    {
        $this->taskSlotChooserHeuristic = $taskSlotChooserHeuristicInterface;
        return $this;
    }

    public function makeAssignment(Planning $planning): Assignment
    {
        foreach ($this->backtrack(new BacktrackableAssignment($planning)) as $assignment) {
            return $assignment;
        }
        throw new ImpossiblePlanningException('Impossible to satisfy the given set of constraints');
    }

    public function makeAssignments(Planning $planning): iterable
    {
        yield from $this->backtrack(new BacktrackableAssignment($planning));
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
        // rejectable constraints
        foreach ($this->constraints as $constraint) {
            if ($constraint instanceof RejectableConstraintInterface
             && $constraint->reject($assignment)) {
                return true;
            }
        }

        // empty domains
        $planning = $assignment->getPlanning();
        for($game = 0 ; $game < $planning->getGameCount() ; ++$game) {
            foreach($planning->getTaskTypes() as $type) {
                if (empty($assignment->getAvailablePersons($game, $type))) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return bool - true if lookahead did not reach a rejected solution
     */
    private function lookahead(BacktrackableAssignment $ba): bool
    {
        do {
            $reduced = false;
            foreach ($this->domainReducers as $reducer) {
                $reduced |= $ba->applyDomainReducer($reducer);
                if ($this->reject($ba)) {
                    return false;
                }
            }
        } while($reduced);

        return true;
    }

    private function pickTaskSlot(BacktrackableAssignment $ba): array
    {
        $heuristic = $this->taskSlotChooserHeuristic;
        return $heuristic($ba);
    }

    private function choosePerson(BacktrackableAssignment $ba, int $game, TaskType $type): iterable
    {
        $heuristic = $this->personChooserHeuristic;
        yield from $heuristic($ba, $game, $type);
    }

    private function backtrack(BacktrackableAssignment $ba): iterable
    {
        $this->backtrackingCalls++;

        if ($this->maxBacktracking > 0 && $this->backtrackingCalls > $this->maxBacktracking) {
            throw new MaximumBacktrackingException('Maximum backtracking of ' . $this->maxBacktracking . ' reached');
        }

        if ($this->validate($ba)) {
            yield $ba->makeAssignment();
            return;
        }

        try {
            [$game, $type] = $this->pickTaskSlot($ba);
        } catch (\Throwable) {
            return;
        }
        foreach ($this->choosePerson($ba, $game, $type) as $person) {
            $ba->setTask($game, $type, $person);
            if ($this->lookahead($ba)) {
                yield from $this->backtrack($ba);
            }
            $ba->unsetLastTask();
        }
    }

    public function getBacktrackingCalls(): int
    {
        return $this->backtrackingCalls;
    }
}