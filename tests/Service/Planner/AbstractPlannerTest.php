<?php

namespace App\Tests\Service\Planner;

use App\Entity\Assignment;
use App\Entity\Person;
use App\Entity\Planning;
use App\Entity\TaskType;
use App\Service\Planner\BasicPlanner;
use App\Service\Planner\ImpossiblePlanningException;
use App\Service\Planner\PlannerInterface;
use App\Validator\Assignment as AssignmentConstraint;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Set of contracts and behaviors expected from any PlannerInterface
 */
abstract class AbstractPlannerTest extends KernelTestCase
{
    use PlannerTestTrait;

    public abstract function getPlanner(): PlannerInterface;

    /**
     * @test
     */
    public function shouldThrowWhenPlanningIsImpossible(): void
    {
        $planning = new Planning;

        $planning->setTitle('Test planning');
        $planning->setGameCount(3);
        $planning->addTaskType((new TaskType)->setName('type 1'));
        $planning->addTaskType((new TaskType)->setName('type 2'));
        $planning->addPerson((new Person)->setName('person 1'));
        //not enough people

        $this->expectException(ImpossiblePlanningException::class);
        $assignment = $this->getPlanner()->makeAssignment($planning);
    }

    /**
     * @test
     */
    public function shouldProvideAnEmptyAssignmentWhenThereIsNothingToDo(): void
    {
        $planning = new Planning;

        $planning->setTitle('Test planning');
        $planning->setGameCount(3);
        $planning->addPerson((new Person)->setName('person 1'));

        $assignment = $this->getPlanner()->makeAssignment($planning);

        $this->assertCount(0, $assignment->getTasks());
    }

    /**
     * @test
     */
    public function shouldProvideAValidAssignment(): void
    {
        $assignment = $this->generateTestAssignment($this->getPlanner(), $this->makePlanning());

        $validator = static::getContainer()->get('validator');
        $errors = $validator->validate($assignment, new AssignmentConstraint);
        
        $this->assertCount(0, $errors);
    }

    /**
     * @test
     * Check that nobody has more than one more assigned task than anyone else
     */
    public function shouldPreventPeopleFromHavingTooManyTasks(): void
    {        
        $assignment = $this->generateTestAssignment($this->getPlanner(), $this->makePlanning());

        $tasksPerPerson = [
            'person 1' => 0,
            'person 2' => 0,
            'person 3' => 0,
            'person 4' => 0,
            'person 5' => 0,
            'person 6' => 0,
        ];
        foreach ($assignment->getTasks() as $task) {
            /** @var Task $task */
            $tasksPerPerson[$task->getAssignee()->__toString()]++;
        }

        $min = min($tasksPerPerson);
        $max = max($tasksPerPerson);
        
        $this->assertLessThanOrEqual(1, $max - $min);
    }
}
