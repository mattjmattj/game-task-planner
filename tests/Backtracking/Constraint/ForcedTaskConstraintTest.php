<?php

namespace App\Tests\Service\Backtracking\Constraint;

use App\Backtracking\Constraint\AssignmentValidatorConstraint;
use App\Backtracking\Constraint\ForcedTaskConstraint;
use App\Backtracking\Constraint\NoSpecialistConstraint;
use App\Backtracking\Constraint\NotTooManyTasksConstraint;
use App\Backtracking\Constraint\UnavailablePersonConstraint;
use App\Entity\Planning;
use App\Service\Planner\BacktrackingPlanner;
use App\Tests\Service\Planner\PlannerTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ForcedTaskConstraintTest extends KernelTestCase
{
    use PlannerTestTrait;

    /**
     * @test
     */
    public function shouldForceATaskToSomeone(): void
    {
        /** @var BacktrackingPlanner */
        $planner = static::getContainer()->get(BacktrackingPlanner::class);
        $validator = static::getContainer()->get('validator');

        $planner->addConstraint(new NotTooManyTasksConstraint);
        $planner->addConstraint(new AssignmentValidatorConstraint($validator));

        $planning = $this->makePlanning(6, 7, 4);

        $planner->addConstraint(new ForcedTaskConstraint(
            0,
            $planning->getTaskTypes()->get(1),
            $planning->getPersons()->get(2)
        ));

        // we make a first planning and then a second one after adding an UnavailablePersonConstraint

        $assignment = $planner->makeAssignment($planning);

        foreach ($assignment->getTasks() as $task) {
            $this->assertTrue(
                $task->getGame() !== 0 
                || $task->getType() !== $planning->getTaskTypes()->get(1)
                || $task->getAssignee() === $planning->getPersons()->get(2)
            );
        }

    }
}
