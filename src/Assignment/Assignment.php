<?php

namespace App\Assignment;

use App\Entity\Planning;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


#[\App\Validator\Assignment]
class Assignment
{
    private Planning $planning;

    /**
     * @var Collection|Task[]
     */
    private ArrayCollection $tasks;

    private string $title;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlanning(): ?Planning
    {
        return $this->planning;
    }

    public function setPlanning(?Planning $planning): self
    {
        $this->planning = $planning;

        return $this;
    }

    /**
     * @return Collection|Task[]
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setAssignment($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getAssignment() === $this) {
                $task->setAssignment(null);
            }
        }

        return $this;
    }

    public function getTasksGroupedByGame(): iterable
    {
        $tasks = [];
        foreach($this->getTasks() as $task) {
            /** @var Task $task */

            $tasks[$task->getGame()][] = $task;
        }
        return $tasks;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function debugPrint()
    {
        foreach ($this->getTasks() as $task) {
            printf("game #%d : %s -> %s\n", $task->getGame(), $task->getType(), $task->getAssignee());
        }
    }

    public function toArray(): array
    {
        $r = [];
        foreach ($this->getTasksGroupedByGame() as $game => $tasks) {
            $r[$game] = [];
            foreach ($tasks as $task) {
                $r[$game][$this->getPlanning()->getTaskTypes()->indexOf($task->getType())] = $task->getAssignee();
            }
        }
        return $r;
    }

    public function equals (Assignment $assignment)
    {
        if ($this->getPlanning() !== $assignment->getPlanning()) {
            return false;
        }
        foreach ($this->getTasks() as $mytask) {
            $found = false;
            foreach ($assignment->getTasks() as $theirtask) {
                if ($mytask->equals($theirtask)) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                return false;
            }
        }
    }
}
