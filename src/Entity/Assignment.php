<?php

namespace App\Entity;

use App\Repository\AssignmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AssignmentRepository::class)
 */
#[\App\Validator\Assignment]
class Assignment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Planning::class, inversedBy="assignments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $planning;

    /**
     * @ORM\OneToMany(targetEntity=Task::class, mappedBy="assignment", orphanRemoval=true)
     */
    private $tasks;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

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
            printf("%d : %s -> %s\n", $task->getGame(), $task->getType(), $task->getAssignee());
        }
    }

    public function toArray(): array
    {
        $r = [];
        foreach ($this->getTasksGroupedByGame() as $game => $tasks) {
            $r[$game] = [];
            foreach ($tasks as $task) {
                $r[$game][$task->getType()] = $task->getAssignee();
            }
        }
        return $r;
    }
}
