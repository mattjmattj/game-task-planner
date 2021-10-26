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
     * @ORM\ManyToMany(targetEntity=Person::class, inversedBy="plannings")
     */
    private $persons;

    /**
     * @ORM\ManyToMany(targetEntity=TaskType::class)
     */
    private $taskTypes;

    /**
     * @ORM\Column(type="integer")
     */
    private $gameCount;

    /**
     * @ORM\OneToMany(targetEntity=Assignment::class, mappedBy="planning", orphanRemoval=true)
     */
    private $assignments;

    public function __construct()
    {
        $this->taskTypes = new ArrayCollection();
        $this->persons = new ArrayCollection();
        $this->assignments = new ArrayCollection();
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

    public function getGameCount(): ?int
    {
        return $this->gameCount;
    }

    public function setGameCount(int $gameCount): self
    {
        $this->gameCount = $gameCount;

        return $this;
    }

    /**
     * @return Collection|Assignment[]
     */
    public function getAssignments(): Collection
    {
        return $this->assignments;
    }

    public function addAssignment(Assignment $assignment): self
    {
        if (!$this->assignments->contains($assignment)) {
            $this->assignments[] = $assignment;
            $assignment->setPlanning($this);
        }

        return $this;
    }

    public function removeAssignment(Assignment $assignment): self
    {
        if ($this->assignments->removeElement($assignment)) {
            // set the owning side to null (unless already changed)
            if ($assignment->getPlanning() === $this) {
                $assignment->setPlanning(null);
            }
        }

        return $this;
    }
}
