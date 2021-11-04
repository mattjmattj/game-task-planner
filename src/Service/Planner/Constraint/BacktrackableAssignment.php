<?php

namespace App\Service\Planner\Constraint;

use App\Entity\Assignment;
use App\Entity\Person;
use App\Entity\Planning;
use App\Entity\Task;
use App\Entity\TaskType;

/**
 * Representation of an Assignment that will ease the implementation of better
 * algorithms and heuristics when using backtraking search in ConstraintBasedPlanner
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

    private array $availableTaskSlots;

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
        }
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
        return $this;
    }

    public function unsetTask(int $game, TaskType $type): self
    {
        $this->taskSlots[$game][$type] = false;
        $this->availableTaskSlots[] = [$game, $type];
        return $this;
    }


    public function getAvailablePersons(int $game, TaskType $type): array
    {
        // we will return the persons not already assigned to a task for this game
        // TODO keep track of the domain of each slot (game x type) along the way
        $assignedPersons = [];
        foreach ($this->taskSlots[$game] as $type) {
            $person = $this->taskSlots[$game][$type];
            if (!!$person) {
                $assignedPersons[] = $person;
            }
        }
        $availablePersons = array_diff(
            $this->planning->getPersons()->toArray(),
            $assignedPersons
        );
        return array_values($availablePersons);
    }

    public function getAvailableTaskSlots(): array
    {
        return array_values($this->availableTaskSlots);
    }

    public function isTaskSlotAvailable(int $game, TaskType $type): bool
    {
        return !$this->taskSlots[$game][$type];
    }
}