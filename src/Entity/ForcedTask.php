<?php

namespace App\Entity;

use App\Repository\ForcedTaskRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ForcedTaskRepository::class)]
class ForcedTask
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TaskType $taskType = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Person $person = null;

    #[ORM\Column]
    private ?int $game = null;

    #[ORM\ManyToOne(inversedBy: 'forcedTasks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Planning $planning = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTaskType(): ?TaskType
    {
        return $this->taskType;
    }

    public function setTaskType(?TaskType $taskType): self
    {
        $this->taskType = $taskType;

        return $this;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson(?Person $person): self
    {
        $this->person = $person;

        return $this;
    }

    public function getGame(): ?int
    {
        return $this->game;
    }

    public function setGame(int $game): self
    {
        $this->game = $game;

        return $this;
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

    public function __toString(): string
    {
        return sprintf("%s => %s on game #%d",
            $this->getPerson()->getName(),
            $this->getTaskType()->__toString(),
            $this->getGame() + 1
        );
    }
}
