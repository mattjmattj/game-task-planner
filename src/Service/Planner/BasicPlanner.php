<?php

namespace App\Service\Planner;

use App\Assignment\Assignment;
use App\Entity\Planning;
use App\Backtracking\Constraint\AssignmentValidatorConstraint;
use App\Backtracking\Constraint\NoSpecialistConstraint;
use App\Backtracking\Constraint\NotTooManyTasksConstraint;
use App\Backtracking\DomainReducer\OneTaskPerGameDomainReducer;
use App\Backtracking\Heuristic\NoSpecialistPersonChooserHeuristic;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Early version of this planner had their own algorithm here, but it is simpler
 * to just configure a BacktrackingPlanner
 */
final class BasicPlanner implements PlannerInterface
{
    public function __construct(
        private BacktrackingPlanner $planner,
        ValidatorInterface $validator
    )
    {
        $this->planner->addConstraint(new AssignmentValidatorConstraint($validator));
        $this->planner->addConstraint(new NotTooManyTasksConstraint);
        $this->planner->addConstraint(new NoSpecialistConstraint);

        $this->planner->setPersonChooserHeuristic(new NoSpecialistPersonChooserHeuristic);

        $this->planner->addDomainReducer(new OneTaskPerGameDomainReducer);
    }

    public function makeAssignment(Planning $planning): Assignment
    {
        return $this->planner->makeAssignment($planning);
    }
}