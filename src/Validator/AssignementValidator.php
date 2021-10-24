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

        // we will keep track of the tasks we meet by person and by type, for each game
        $types = $persons = [];
        
        foreach ($assignement->getTasks() as $task) {
            /** @var Task $task */

            $type = $task->getType()->__toString();
            $person = $task->getAssignee()->__toString();
            $game = $task->getGame();
            
            if (isset($types[$game][$type])) {
                $this->context->buildViolation($constraint->message)
                    ->setCode(Assignement::DUPLICATED_TASKS_ERROR)
                    ->addViolation();
            }

            if (isset($persons[$game][$person])) {
                $this->context->buildViolation($constraint->message)
                    ->setCode(Assignement::MULTIPLE_TASKS_ERROR)
                    ->addViolation();
            }

            $types[$game][$type] = true;
            $persons[$game][$person] = true;
        }
    }
}
