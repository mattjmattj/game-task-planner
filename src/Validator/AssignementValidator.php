<?php

namespace App\Validator;

use App\Entity\Assignement as AssignementEntity;
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
    }
}
