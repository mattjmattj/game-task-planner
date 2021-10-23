<?php

namespace App\Entity;

use App\Repository\PlanningRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlanningRepository::class)
 */
class Planning
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $title;

    /**
     * @ORM\OneToMany(targetEntity=Game::class, mappedBy="planning", orphanRemoval=true)
     */
    private $games;

    /**
     * @ORM\OneToMany(targetEntity=TaskType::class, mappedBy="planning", orphanRemoval=true)
     */
    private $taskTypes;

    /**
     * @ORM\ManyToMany(targetEntity=Person::class, inversedBy="plannings")
     */
    private $persons;

    /**
     * @ORM\OneToMany(targetEntity=Task::class, mappedBy="planning", orphanRemoval=true)
     */
    private $tasks;

    public function __construct()
    {
        $this->games = new ArrayCollection();
        $this->taskTypes = new ArrayCollection();
        $this->persons = new ArrayCollection();
        $this->tasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection|Game[]
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): self
    {
        if (!$this->games->contains($game)) {
            $this->games[] = $game;
            $game->setPlanning($this);
        }

        return $this;
    }

    public function removeGame(Game $game): self
    {
        if ($this->games->removeElement($game)) {
            // set the owning side to null (unless already changed)
            if ($game->getPlanning() === $this) {
                $game->setPlanning(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TaskType[]
     */
    public function getTaskTypes(): Collection
    {
        return $this->taskTypes;
    }

    public function addTaskType(TaskType $taskType): self
    {
        if (!$this->taskTypes->contains($taskType)) {
            $this->taskTypes[] = $taskType;
            $taskType->setPlanning($this);
        }

        return $this;
    }

    public function removeTaskType(TaskType $taskType): self
    {
        if ($this->taskTypes->removeElement($taskType)) {
            // set the owning side to null (unless already changed)
            if ($taskType->getPlanning() === $this) {
                $taskType->setPlanning(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Person[]
     */
    public function getPersons(): Collection
    {
        return $this->persons;
    }

    public function addPerson(Person $person): self
    {
        if (!$this->persons->contains($person)) {
            $this->persons[] = $person;
        }

        return $this;
    }

    public function removePerson(Person $person): self
    {
        $this->persons->removeElement($person);

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
            $task->setPlanning($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getPlanning() === $this) {
                $task->setPlanning(null);
            }
        }

        return $this;
    }
}
