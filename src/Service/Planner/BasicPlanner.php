<?php

namespace App\Service\Planner;

use App\Assignment\Assignment;
use App\Entity\Planning;
use App\Backtracking\Constraint\AssignmentValidatorConstraint;
use App\Backtracking\Constraint\NoSpecialistConstraint;
use App\Backtracking\Constraint\NotTooManyTasksConstraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Early version of this planner had their own algorithm here, but it is simpler
 * to just configure a BacktrackingPlanner
 */
final class BasicPlanner implements PlannerInterface
{
    public function __construct(
        private BacktrackingPlanner $planner,
        private ValidatorInterface $validator
    )
    {}

    public function makeAssignment(Planning $planning): Assignment
    {
        $this->planner->addConstraint(new AssignmentValidatorConstraint($this->validator));
        $this->planner->addConstraint(new NotTooManyTasksConstraint);
        $this->planner->addConstraint(new NoSpecialistConstraint);

        return $this->planner->makeAssignment($planning);
    }
}