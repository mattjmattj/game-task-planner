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
    private $gameNumber;

    /**
     * @ORM\OneToMany(targetEntity=Assignement::class, mappedBy="planning", orphanRemoval=true)
     */
    private $assignements;

    public function __construct()
    {
        $this->taskTypes = new ArrayCollection();
        $this->persons = new ArrayCollection();
        $this->assignements = new ArrayCollection();
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

    public function getGameNumber(): ?int
    {
        return $this->gameNumber;
    }

    public function setGameNumber(int $gameNumber): self
    {
        $this->gameNumber = $gameNumber;

        return $this;
    }

    /**
     * @return Collection|Assignement[]
     */
    public function getAssignements(): Collection
    {
        return $this->assignements;
    }

    public function addAssignement(Assignement $assignement): self
    {
        if (!$this->assignements->contains($assignement)) {
            $this->assignements[] = $assignement;
            $assignement->setPlanning($this);
        }

        return $this;
    }

    public function removeAssignement(Assignement $assignement): self
    {
        if ($this->assignements->removeElement($assignement)) {
            // set the owning side to null (unless already changed)
            if ($assignement->getPlanning() === $this) {
                $assignement->setPlanning(null);
            }
        }

        return $this;
    }
}
