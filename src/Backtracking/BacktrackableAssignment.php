<?php

namespace App\Backtracking;

use App\Assignment\Assignment;
use App\Assignment\Task;
use App\Entity\Person;
use App\Entity\Planning;
use App\Entity\TaskType;

/**
 * Representation of an Assignment that will ease the implementation of better
 * algorithms and heuristics when using backtraking search in BacktrackingPlanner
 * 
 * TODO maybe count some stuff along the way, to improve the performance of some algorithms
 * or maintain the _domain_ of each task slot, for early rejections
 */
final class BacktrackableAssignment
{
    /**
     * the list of all variables
     * game -> type -> person
     * @var \SplObjectStorage[]
     */
    private array $taskSlots;

    /**
     * the lsit of all variables, but grouped by task type
     * type -> game -> person
     */
    private \SplObjectStorage $taskSlotsPerType;

    /**
     * the list of all task slots not already assigned
     * @var (int, TaskType)[]
     */
    private array $availableTaskSlots = [];

    /**
     * how many tasks each person have assigned to them
     */
    private \SplObjectStorage $taskCountPerPerson;

    /**
     * stack of Domain, defining the domain of each taskSlot.
     * we use a stack in order to easily go back to a previous state
     */
    private \SplStack $domainStack;

    /**
     * @see unsetLastTask
     */
    private \SplStack $lastTaskSlotStack;

    public function __construct(
        private Planning $planning
    )
    {
        $this->taskSlotsPerType = new \SplObjectStorage;
        
        for ($game = 0 ; $game < $this->planning->getGameCount() ; ++$game) {
            $this->taskSlots[$game] = new \SplObjectStorage;
            foreach ($this->planning->getTaskTypes() as $type) {
                $this->taskSlots[$game][$type] = false;
                $this->availableTaskSlots[] = [$game, $type];
                if (!$this->taskSlotsPerType->contains($type)) {
                    $this->taskSlotsPerType[$type] = [];
                }
                $a = $this->taskSlotsPerType->contains($type)
                    ? $this->taskSlotsPerType[$type]
                    : [];
                $a[$game] = false;
                $this->taskSlotsPerType[$type] = $a;
            }
        }

        $this->taskCountPerPerson = new \SplObjectStorage;
        foreach ($this->planning->getPersons() as $person) {
            $this->taskCountPerPerson[$person] = 0;
        }

        $this->domainStack = new \SplStack;
        $this->domainStack->push(new Domain($this->planning));

        $this->lastTaskSlotStack = new \SplStack;
    }

    public static function fromAssignment(Assignment $assignment): self
    {
        $ba = new self($assignment->getPlanning());
        foreach ($assignment->getTasks() as $task) {
            /** @var Task $task */
            $ba->setTask($task->getGame(), $task->getType(), $task->getAssignee());
        }
        return $ba;
    }
    
    public function makeAssignment(): Assignment
    {
        $assignment = new Assignment;
        $assignment->setPlanning($this->planning);
        foreach ($this->taskSlots as $game => $gameTaskSlots) {
            foreach ($gameTaskSlots as $type) {
                $person = $gameTaskSlots[$type];
                if (!!$person) {
                    $assignment->addTask(
                        (new Task)
                            ->setGame($game)
                            ->setType($type)
                            ->setAssignee($person)
                    );
                }
            }
        }
        return $assignment;
    }

    public function setTask(int $game, TaskType $type, Person $person): self
    {
        $this->taskSlots[$game][$type] = $person;

        $a = $this->taskSlotsPerType[$type];
        $a[$game] = $person;
        $this->taskSlotsPerType[$type] = $a;

        $k = array_search([$game, $type], $this->availableTaskSlots, true);
        if (false !== $k) {
            unset($this->availableTaskSlots[$k]);
        }

        $domain = $this->domainStack->top();

        $newDomain = Domain::createCopy($domain);
        $newDomain->setDomain($game, $type, [$person]);
        $this->domainStack->push($newDomain);

        $this->taskCountPerPerson[$person] = $this->taskCountPerPerson[$person] + 1;

        $this->lastTaskSlotStack->push([$game, $type]);

        return $this;
    }

    /**
     * Reduces the current domain with a given list of reducers
     */
    public function applyDomainReducers(iterable $domainReducers)
    {
        $domain = $this->domainStack->top();
        do {
            $changed = false;
            foreach($domainReducers as $reducer) {
                $changed |= $reducer($domain);
            }
        } while ($changed);
    }

    public function unsetLastTask(): self
    {
        if ($this->lastTaskSlotStack->isEmpty()) {
            return $this;
        }

        [$game, $type] = $this->lastTaskSlotStack->pop();

        $person = $this->taskSlots[$game][$type];
        $this->taskSlots[$game][$type] = false;

        $a = $this->taskSlotsPerType[$type];
        $a[$game] = false;
        $this->taskSlotsPerType[$type] = $a;

        $this->availableTaskSlots[] = [$game, $type];
        if ($person !== false) {
            $this->taskCountPerPerson[$person] = $this->taskCountPerPerson[$person] - 1;
        }

        $this->domainStack->pop();

        return $this;
    }


    public function getAvailablePersons(int $game, TaskType $type): array
    {
        /** @var Domain */
        $domain = $this->domainStack->top();
        return $domain->getDomain($game, $type);
    }

    public function getAvailableTaskSlots(): array
    {
        return array_values($this->availableTaskSlots);
    }

    public function isTaskSlotAvailable(int $game, TaskType $type): bool
    {
        return !$this->taskSlots[$game][$type];
    }

    public function getTaskCount(Person $person): int
    {
        return $this->taskCountPerPerson[$person];
    }

    public function getPlanning(): Planning
    {
        return $this->planning;
    }

    public function getTaskSlots(): array
    {
        return $this->taskSlots;
    }

    public function getTaskSlotsPerType(): \SplObjectStorage
    {
        return $this->taskSlotsPerType;
    }

    public function getTaskSlotsByType(TaskType $taskType): array
    {
        return $this->taskSlotsPerType[$taskType];
    }

    public function equals(BacktrackableAssignment $ba): bool
    {
        return 
            $this->planning === $ba->planning
            && $this->taskSlots == $ba->taskSlots;
    }
}