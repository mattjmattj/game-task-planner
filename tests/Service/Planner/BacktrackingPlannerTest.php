<?php

namespace App\Tests\Service\Planner;

use App\Assignment\Assignment;
use App\Entity\Task;
use App\Backtracking\Constraint\ConstraintInterface;
use App\Backtracking\Constraint\AssignmentValidatorConstraint;
use App\Backtracking\Constraint\NoSpecialistConstraint;
use App\Backtracking\Constraint\NotTooManyTasksConstraint;
use App\Backtracking\BacktrackableAssignment;
use App\Backtracking\Constraint\NotTwiceTheSameTaskConstraint;
use App\Backtracking\Constraint\RejectableConstraintInterface;
use App\Backtracking\DomainReducer\NotTwiceTheSameTaskDomainReducer;
use App\Backtracking\DomainReducer\OneTaskPerGameDomainReducer;
use App\Backtracking\Heuristic\SmallestDomainTaskSlotChooserHeuristic;
use App\Backtracking\Heuristic\NoSpecialistPersonChooserHeuristic;
use App\Service\Planner\BacktrackingPlanner;
use App\Service\Planner\ImpossiblePlanningException;
use App\Service\Planner\MaximumBacktrackingException;
use App\Service\Planner\PlannerInterface;

/**
 * A BacktrackingPlanner is a more evolved planner, that must obey
 * the base contract of any planner, but also make sure that the returned
 * Assignment is compatible with a provided set of constraints
 */
class BacktrackingPlannerTest extends AbstractPlannerTest
{
    protected BacktrackingPlanner $planner;

    public function setUp(): void
    {
        $this->planner = static::getContainer()->get(BacktrackingPlanner::class);
        $validator = static::getContainer()->get('validator');

        // we add this constraint in order to implement the needed 
        // contracts defined in AbstractPlannerTest
        $this->planner->addConstraint(new NotTooManyTasksConstraint);
        $this->planner->addConstraint(new AssignmentValidatorConstraint($validator));

        // + no specialist
        $this->planner->addConstraint(new NoSpecialistConstraint);
        $this->planner->addConstraint(new NotTwiceTheSameTaskConstraint);

        $this->planner->setPersonChooserHeuristic(new NoSpecialistPersonChooserHeuristic);
        $this->planner->setTaskSlotChooserHeuristic(new SmallestDomainTaskSlotChooserHeuristic);

        $this->planner->setMaxBacktracking(100000);

        $this->planner->addDomainReducer(new NotTwiceTheSameTaskDomainReducer);
        $this->planner->addDomainReducer(new OneTaskPerGameDomainReducer);
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
        $dummy1 = new class() implements RejectableConstraintInterface {
            public function reject(BacktrackableAssignment $assignment): bool
            {
                $taskSlots = $assignment->getTaskSlots();
                $planning = $assignment->getPlanning();
                $value = $taskSlots[0][$planning->getTaskTypes()->get(1)];
                return 
                    $value !== false
                    && $value !== $planning->getPersons()->get(0);
            }

            public function validate(BacktrackableAssignment $assignment): bool
            {
                return !$this->reject($assignment);
            }
        };

        // dummy constraint that wants person 2 to do task type 1 on game 1
        $dummy2 = new class() implements RejectableConstraintInterface {
            public function reject(BacktrackableAssignment $assignment): bool
            {
                $taskSlots = $assignment->getTaskSlots();
                $planning = $assignment->getPlanning();
                $value = $taskSlots[1][$planning->getTaskTypes()->get(0)];
                return 
                    $value !== false
                    && $value !== $planning->getPersons()->get(1);
            }

            public function validate(BacktrackableAssignment $assignment): bool
            {
                return !$this->reject($assignment);
            }
        };

        $this->planner->addConstraint($dummy1);
        $this->planner->addConstraint($dummy2);

        // P(3, 2)^2 = 36
        $assignment = $this->generateTestAssignment($this->planner, $this->makePlanning(2, 3, 2));

        $ba = BacktrackableAssignment::fromAssignment($assignment);
        $this->assertTrue($dummy1->validate($ba));
        $this->assertTrue($dummy2->validate($ba));

        // P(5, 2)^6 = 46,656,000,000
        $assignment = $this->generateTestAssignment($this->planner, $this->makePlanning(6, 5, 2));
        
        $ba = BacktrackableAssignment::fromAssignment($assignment);
        $this->assertTrue($dummy1->validate($ba));
        $this->assertTrue($dummy2->validate($ba));

        // P(7, 4)^6 ~= 3.5e17
        $assignment = $this->generateTestAssignment($this->planner, $this->makePlanning(6, 7, 4));

        $ba = BacktrackableAssignment::fromAssignment($assignment);
        $this->assertTrue($dummy1->validate($ba));
        $this->assertTrue($dummy2->validate($ba));
        // $assignment->debugPrint();
        //var_dump($this->planner->getBacktrackingCalls());
    }

    /**
     * @test
     */
    public function shouldThrowWhenPlanningIsImpossibleWithTheGivenConstraints()
    {
        $this->planner->addConstraint(
            new class() implements ConstraintInterface {
                public function validate(BacktrackableAssignment $assignment): bool
                {
                    return false;
                }
            }
        );

        $this->expectException(ImpossiblePlanningException::class);
        $this->generateTestAssignment($this->planner, $this->makePlanning(2, 3, 2));
    }

    /**
     * @test
     * Check that nobody is given more of a specific type of task than anyone else
     */
    public function shouldPreventAssigningTheSameTaskTypeToTheSamePeople(): void
    {
        $assignment = $this->generateTestAssignment($this->planner, $this->makePlanning(6, 7, 4));

        $details = [
            'T0' => [
                'P0' => 0,
                'P1' => 0,
                'P2' => 0,
                'P3' => 0,
                'P4' => 0,
                'P5' => 0,
                'P6' => 0,
            ],
            'T1' => [
                'P0' => 0,
                'P1' => 0,
                'P2' => 0,
                'P3' => 0,
                'P4' => 0,
                'P5' => 0,
                'P6' => 0,
            ],
            'T2' => [
                'P0' => 0,
                'P1' => 0,
                'P2' => 0,
                'P3' => 0,
                'P4' => 0,
                'P5' => 0,
                'P6' => 0,
            ],
            'T3' => [
                'P0' => 0,
                'P1' => 0,
                'P2' => 0,
                'P3' => 0,
                'P4' => 0,
                'P5' => 0,
                'P6' => 0,
            ],
        ];

        foreach ($assignment->getTasks() as $task) {
            /** @var Task $task */
            $details[$task->getType()->__toString()][$task->getAssignee()->__toString()]++;
        }

        foreach ($details as $type => $tasksPerPerson) {
            $min = min($tasksPerPerson);
            $max = max($tasksPerPerson);
        
            $this->assertLessThanOrEqual(1, $max - $min);
        }
    }

    /**
     * @test
     */
    public function shouldThrowWhenMaximumBacktrackingIsReached()
    {
        $this->expectException(MaximumBacktrackingException::class);
        $this->planner->setMaxBacktracking(10);

        $this->planner->makeAssignment($this->makePlanning());
    }

    /**
     * @test
     */
    public function shouldMakeAssignmentsForBigProblems()
    {
        // P(6,8)^6 = 6,7×10²⁵
        $assignment = $this->generateTestAssignment($this->planner, $this->makePlanning(6, 8, 6));
        $this->assertInstanceOf(Assignment::class, $assignment);
        // echo PHP_EOL;
        // echo $this->planner->getBacktrackingCalls() . " backtracks\n";
        // $assignment->debugPrint();
    }

    /**
     * @test
     * using a BacktrackingPlanner as a generator
     */
    public function shouldMakeMultipleAssignements()
    {
        $planning = $this->makePlanning(6, 7, 4);
        $count = 10;
        $assignments = [];
        foreach ($this->planner->makeAssignments($planning) as $assignment) {
            if (--$count === 0) {
                break;
            }
            // echo PHP_EOL;
            // echo $this->planner->getBacktrackingCalls() . " backtracks\n";
            // $assignment->debugPrint();

            foreach($assignments as $old) {
                $this->assertFalse($old->equals($assignment));
            }
            $assignments[] = $assignment;
        }
    }
}