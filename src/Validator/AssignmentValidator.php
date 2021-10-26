<?php

namespace App\Validator;

use App\Entity\Assignment as AssignmentEntity;
use App\Entity\Task;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AssignmentValidator extends ConstraintValidator
{
    
    public function validate($assignment, Constraint $constraint)
    {
        if (!$constraint instanceof Assignment) {
            return;
        }

        if (!$assignment instanceof AssignmentEntity) {
            return;
        }

        $taskTypes = $assignment->getPlanning()->getTaskTypes();
        $gameCount = $assignment->getPlanning()->getGameCount();
        $totalExpectedTasks = count($taskTypes) * $gameCount;

        if (count($assignment->getTasks()) !== $totalExpectedTasks) {
            $this->context->buildViolation($constraint->message)
                ->setCode(Assignment::MISSING_TASKS_ERROR)
                ->addViolation();
        }

        // we will keep track of the tasks we meet by person and by type, for each game
        $types = $persons = [];
        
        foreach ($assignment->getTasks() as $task) {
            /** @var Task $task */

            $type = $task->getType()->__toString();
            $person = $task->getAssignee()->__toString();
            $game = $task->getGame();
            
            if (isset($types[$game][$type])) {
                $this->context->buildViolation($constraint->message)
                    ->setCode(Assignment::DUPLICATED_TASKS_ERROR)
                    ->addViolation();
            }

            if (isset($persons[$game][$person])) {
                $this->context->buildViolation($constraint->message)
                    ->setCode(Assignment::MULTIPLE_TASKS_ERROR)
                    ->addViolation();
            }

            $types[$game][$type] = true;
            $persons[$game][$person] = true;
        }
    }
}
