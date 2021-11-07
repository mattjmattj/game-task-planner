<?php

namespace App\Entity;

class Task
{
    private Person $assignee;

    private TaskType $type;

    private int $game;

    private Assignment $assignment;

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

    public function equals(Task $task): bool
    {
        return $this->getAssignee() === $task->getAssignee()
            && $this->getGame() === $task->getGame()
            && $this->getType() === $task->getType();
    }

}
