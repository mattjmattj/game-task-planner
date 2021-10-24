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

        $tasks = $assignement->getTasksGroupedByGame();

        foreach ($tasks as $game => $tasksForGame) {
            /** @var Task $task */
            
            $types = $persons = [];

            foreach ($tasksForGame as $task) {
                $type = $task->getType()->__toString();
                $person = $task->getAssignee()->__toString();
                
                if (isset($types[$type])) {
                    $this->context->buildViolation($constraint->message)
                        ->setCode(Assignement::DUPLICATED_TASKS_ERROR)
                        ->addViolation();
                }

                if (isset($persons[$person])) {
                    $this->context->buildViolation($constraint->message)
                        ->setCode(Assignement::MULTIPLE_TASKS_ERROR)
                        ->addViolation();
                }

                $types[$type] = true;
                $persons[$person] = true;
            }
        }
    }
}
