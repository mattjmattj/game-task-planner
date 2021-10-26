<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 */
class Task
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Person::class, inversedBy="tasks")
     */
    private $assignee;

    /**
     * @ORM\ManyToOne(targetEntity=TaskType::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=Assignment::class, inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $assignment;

    /**
     * @ORM\Column(type="integer")
     */
    private $game;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAssignee(): ?Person
    {
        return $this->assignee;
    }

    public function setAssignee(?Person $assignee): self
    {
        $this->assignee = $assignee;

        return $this;
    }

    public function getType(): ?TaskType
    {
        return $this->type;
    }

    public function setType(?TaskType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getAssignment(): ?Assignment
    {
        return $this->assignment;
    }

    public function setAssignment(?Assignment $assignment): self
    {
        $this->assignment = $assignment;

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

}
