<?php

namespace App\Validator;

use App\Entity\Assignement as AssignementEntity;
use App\Entity\Task;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AssignementValidator extends ConstraintValidator
{
    
    public function validate($assignement, Constraint $constraint)
    {
        if (!$constraint instanceof Assignement) {
            return;
        }

        if (!$assignement instanceof AssignementEntity) {
            return;
        }

        $taskTypes = $assignement->getPlanning()->getTaskTypes();
        $gameCount = $assignement->getPlanning()->getGameCount();
        $totalExpectedTasks = count($taskTypes) * $gameCount;

        if (count($assignement->getTasks()) !== $totalExpectedTasks) {
            $this->context->buildViolation($constraint->message)
                ->setCode(Assignement::MISSING_TASKS_ERROR)
                ->addViolation();
        }

        foreach ($assignement->getTasks() as $task) {
            /** @var Task $task */
            if ($this->isTaskDuplicated($task, $assignement->getTasks())) {
                $this->context->buildViolation($constraint->message)
                    ->setCode(Assignement::DUPLICATED_TASKS_ERROR)
                    ->addViolation();
                
                // duplication errors can get messy, so we break at the first one
                // in order to keep errors under control
                break;
            }

            if ($this->isAssigneeDuplicated($task, $assignement->getTasks())) {
                $this->context->buildViolation($constraint->message)
                    ->setCode(Assignement::MULTIPLE_TASKS_ERROR)
                    ->addViolation();
                
                // duplication errors can get messy, so we break at the first one
                // in order to keep errors under control
                break;
            }

        }
    }

    private function isTaskDuplicated(Task $task, iterable $tasks): bool
    {
        foreach ($tasks as $comparedTask) {
            /** @var Task $comparedTask */
            if ($task === $comparedTask) {
                continue;
            }
            if ($task->getGame() === $comparedTask->getGame()
                && $task->getType() === $comparedTask->getType()) {
                return true;
            }
        }
        return false;
    }

    private function isAssigneeDuplicated(Task $task, iterable $tasks): bool
    {
        foreach ($tasks as $comparedTask) {
            /** @var Task $comparedTask */
            if ($task === $comparedTask) {
                continue;
            }
            if ($task->getGame() === $comparedTask->getGame()
                && $task->getAssignee() === $comparedTask->getAssignee()) {
                return true;
            }
        }
        return false;
    }
}
