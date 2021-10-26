<?php

namespace App\Tests\Service\Planner;

use App\Entity\Assignment;
use App\Entity\Task;
use App\Service\AssignmentGenerator;
use App\Service\Planner\Constraint\ConstraintInterface;
use App\Service\Planner\BasicPlanner;
use App\Service\Planner\ConstraintBasedPlanner;
use App\Service\Planner\PlannerInterface;

/**
 * A ConstraintBasedPlanner is a more evolved planner, that must obey
 * the base contract of any planner, but also make sure that the returned
 * Assignment is compatible with a provided set of constraints
 */
class ConstraintBasedPlannerTest extends AbstractPlannerTest
{
    protected ConstraintBasedPlanner $planner;

    public function setUp(): void
    {
        $this->planner = new ConstraintBasedPlanner(new AssignmentGenerator);
    }

    public function getPlanner(): PlannerInterface
    {
        return $this->planner;
    }

    /**
     * @test
     */
    public function shouldProvideAnAssignmentValidatingASetOfConstraints()
    {   
        // dummy constraint that wants person 1 to do task type 2 on game 0
        $dummy1 = new class() implements ConstraintInterface {
            public function validate(Assignment $assignment): bool
            {
                foreach($assignment->getTasks() as $task) {
                    /** @var Task $task */
                    if ($task->getType()->getName() === 'type 2'
                        && $task->getGame() === 0) {
                        return 'person 1' === $task->getAssignee()->getName();
                    }
                }
                return false;
            }
        };

        // dummy constraint that wants person 2 to do task type 1 on game 1
        $dummy2 = new class() implements ConstraintInterface {
            public function validate(Assignment $assignment): bool
            {
                foreach($assignment->getTasks() as $task) {
                    /** @var Task $task */
                    if ($task->getType()->getName() === 'type 1'
                        && $task->getGame() === 1) {
                        return 'person 2' === $task->getAssignee()->getName();
                    }
                }
                return false;
            }
        };

        $this->planner->setConstraints([$dummy1, $dummy2]);
        $assignment = $this->generateTestAssignment($this->planner, $this->makePlanning());

        $this->assertTrue($dummy1->validate($assignment));
        $this->assertTrue($dummy2->validate($assignment));

    }
}