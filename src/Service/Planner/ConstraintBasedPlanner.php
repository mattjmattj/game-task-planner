<?php

namespace App\Service\Planner;

use App\Entity\Assignement;
use App\Entity\Planning;
use App\Entity\Task;
use App\Service\AssignementGenerator;
use App\Service\Planner\Constraint\ConstraintInterface;

final class ConstraintBasedPlanner implements PlannerInterface
{
    /** @var ConstraintInterface[] */
    private array $constraints = [];

    public function __construct(
        private AssignementGenerator $assignementGenerator
    )
    {}

    public function setConstraints(array $constraints): self
    {
        $this->constraints = $constraints;
        return $this;
    }

    public function makeAssignement(Planning $planning): Assignement
    {
        foreach ($this->assignementGenerator->assignements($planning) as /** @var Assignement */ $assignement) {
            $ok = true;
            foreach ($this->constraints as /** @var ConstraintInterface */ $constraint) {
                if (!$constraint->validate($assignement)) {
                    $ok = false;
                    break;
                }
            }
            if ($ok) {
                return $assignement;
            }
        }

        throw new ImpossiblePlanningException('Impossible to satisfy the given set of constraints');
    }
}