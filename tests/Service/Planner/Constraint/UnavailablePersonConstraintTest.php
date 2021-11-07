<?php

namespace App\Tests\Service\Planner\Constraint;

use App\Backtracking\Constraint\AssignmentValidatorConstraint;
use App\Backtracking\Constraint\NoSpecialistConstraint;
use App\Backtracking\Constraint\NotTooManyTasksConstraint;
use App\Backtracking\Constraint\UnavailablePersonConstraint;
use App\Entity\Planning;
use App\Service\Planner\BacktrackingPlanner;
use App\Tests\Service\Planner\PlannerTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UnavailablePersonConstraintTest extends KernelTestCase
{
    use PlannerTestTrait;

    /**
     * @test
     */
    public function shouldForbidAssigningATaskToSomeone(): void
    {
        /** @var BacktrackingPlanner */
        $planner = static::getContainer()->get(BacktrackingPlanner::class);
        $validator = static::getContainer()->get('validator');

        $planner->addConstraint(new NotTooManyTasksConstraint);
        $planner->addConstraint(new AssignmentValidatorConstraint($validator));
        $planner->addConstraint(new NoSpecialistConstraint);

        $planning = $this->makePlanning(6, 7, 4);

        // we make a first planning and then a second one after adding an UnavailablePersonConstraint

        $assignment = $planner->makeAssignment($planning);

        /** @var Task */
        $whateverTask = $assignment->getTasks()->get(5);

        $constraint = new UnavailablePersonConstraint($whateverTask->getAssignee(), $whateverTask->getGame());
        $planner->addConstraint($constraint);

        $newAssignment = $planner->makeAssignment($planning);

        foreach ($newAssignment->getTasks() as $task) {
            $this->assertTrue(
                $task->getGame() !== $whateverTask->getGame()
                || $task->getAssignee() !== $whateverTask->getAssignee()
            );
        }

    }
}
