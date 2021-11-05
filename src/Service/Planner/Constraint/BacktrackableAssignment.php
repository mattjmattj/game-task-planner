<?php

namespace App\Service\Planner\Constraint;

use App\Entity\Assignment;
use App\Entity\Person;
use App\Entity\Planning;
use App\Entity\Task;
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
     * @var \SplObjectStorage[]
     */
    private array $taskSlots;

    private array $availableTaskSlots = [];

    private array $availablePersons = [];

    private \SplObjectStorage $taskCountPerPerson;

    public function __construct(
        private Planning $planning
    )
    {
        for ($game = 0 ; $game < $this->planning->getGameCount() ; ++$game) {
            $this->taskSlots[$game] = new \SplObjectStorage;
            foreach ($this->planning->getTaskTypes() as $type) {
                $this->taskSlots[$game][$type] = false;
                $this->availableTaskSlots[] = [$game, $type];
            }
            foreach ($this->planning->getPersons() as $person) {
                $this->availablePersons[$game][] = $person;
            }
        }

        $this->taskCountPerPerson = new \SplObjectStorage;
        foreach ($this->planning->getPersons() as $person) {
            $this->taskCountPerPerson[$person] = 0;
        }
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

        $k = array_search([$game, $type], $this->availableTaskSlots, true);
        if (false !== $k) {
            unset($this->availableTaskSlots[$k]);
        }

        $k = array_search($person, $this->availablePersons[$game]);
        if (false !== $k) {
            unset($this->availablePersons[$game][$k]);
        }

        $this->taskCountPerPerson[$person] = $this->taskCountPerPerson[$person] + 1;

        return $this;
    }

    public function unsetTask(int $game, TaskType $type): self
    {
        $person = $this->taskSlots[$game][$type];
        $this->taskSlots[$game][$type] = false;
        $this->availableTaskSlots[] = [$game, $type];
        if ($person !== false) {
            $this->availablePersons[$game][] = $person;
            $this->taskCountPerPerson[$person] = $this->taskCountPerPerson[$person] - 1;
        }
        return $this;
    }


    public function getAvailablePersons(int $game, TaskType $type): array
    {
        // we will return the persons not already assigned to a task for this game
        // TODO keep track of the domain of each slot (game x type) along the way
        return array_values($this->availablePersons[$game]);
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
}