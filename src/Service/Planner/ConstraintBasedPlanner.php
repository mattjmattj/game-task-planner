<?php

namespace App\Service\Planner;

use App\Entity\Assignment;
use App\Entity\Planning;
use App\Entity\Task;
use App\Service\AssignmentGenerator;
use App\Service\Planner\Constraint\ConstraintInterface;

final class ConstraintBasedPlanner implements PlannerInterface
{
    /** @var ConstraintInterface[] */
    private array $constraints = [];

    public function __construct(
        private AssignmentGenerator $assignmentGenerator
    )
    {}

    public function setConstraints(array $constraints): self
    {
        $this->constraints = $constraints;
        return $this;
    }

    public function makeAssignment(Planning $planning): Assignment
    {
        foreach ($this->assignmentGenerator->assignments($planning) as /** @var Assignment */ $assignment) {
            $ok = true;
            foreach ($this->constraints as /** @var ConstraintInterface */ $constraint) {
                if (!$constraint->validate($assignment)) {
                    $ok = false;
                    break;
                }
            }
            if ($ok) {
                return $assignment;
            }
        }

        throw new ImpossiblePlanningException('Impossible to satisfy the given set of constraints');
    }
}