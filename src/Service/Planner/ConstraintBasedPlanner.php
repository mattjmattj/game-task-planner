<?php

namespace App\Service\Planner;

use App\Entity\Assignement;
use App\Entity\Planning;
use App\Entity\Task;

final class ConstraintBasedPlanner implements PlannerInterface
{
    public function __construct(
        private PlannerInterface $decoratedPlanner,

        /** @var Constraint\ConstraintInterface[] */
        private array $constraints = []
    )
    {}

    public function setConstraints(array $constraints): self
    {
        $this->constraints = $constraints;
        return $this;
    }

    public function makeAssignement(Planning $planning): Assignement
    {
        return $this->decoratedPlanner->makeAssignement($planning);
    }
}