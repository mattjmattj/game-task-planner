<?php

namespace App\Tests\Service\Planner;

use App\Entity\Assignement;
use App\Entity\Person;
use App\Entity\Planning;
use App\Entity\TaskType;
use App\Service\Planner\ImpossiblePlanningException;
use App\Service\Planner\PlannerInterface;
use App\Validator\Assignement as AssignementConstraint;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PlannerTest extends KernelTestCase
{
    private function makePlanning(): Planning
    {
        $planning = new Planning;

        $planning->setTitle('Test planning');
        $planning->setGameCount(6);
        $planning->addTaskType((new TaskType)->setName('type 1'));
        $planning->addTaskType((new TaskType)->setName('type 2'));
        $planning->addTaskType((new TaskType)->setName('type 3'));
        $planning->addTaskType((new TaskType)->setName('type 4'));
        $planning->addPerson((new Person)->setName('person 1'));
        $planning->addPerson((new Person)->setName('person 2'));
        $planning->addPerson((new Person)->setName('person 3'));
        $planning->addPerson((new Person)->setName('person 4'));
        $planning->addPerson((new Person)->setName('person 5'));
        $planning->addPerson((new Person)->setName('person 6'));

        return $planning;
    }

    /**
     * Shortcut method for generating an assignement
     */
    private function generateTestAssignement(): Assignement
    {
        static $assignement;
        if (!isset($assignement)) {
            $kernel = self::bootKernel();
            $this->assertSame('test', $kernel->getEnvironment());

            $planner = static::getContainer()->get(PlannerInterface::class);

            $planning = $this->makePlanning();

            $assignement = $planner->makeAssignement($planning);

            $this->assertInstanceOf(Assignement::class, $assignement);
    
            $this->assertEquals($planning, $assignement->getPlanning());
        }
        return $assignement;
    }

    /**
     * @test
     */
    public function isCorrectlyConfiguredAsAService(): void
    {
        $kernel = self::bootKernel();

        $this->assertSame('test', $kernel->getEnvironment());

        $planner = static::getContainer()->get(PlannerInterface::class);
        $this->assertInstanceOf(PlannerInterface::class, $planner);
    }

    /**
     * @test
     */
    public function shouldThrowWhenPlanningIsImpossible(): void
    {
        $kernel = self::bootKernel();
        $this->assertSame('test', $kernel->getEnvironment());

        $planner = static::getContainer()->get(PlannerInterface::class);

        $planning = new Planning;

        $planning->setTitle('Test planning');
        $planning->setGameCount(3);
        $planning->addTaskType((new TaskType)->setName('type 1'));
        $planning->addTaskType((new TaskType)->setName('type 2'));
        $planning->addPerson((new Person)->setName('person 1'));
        //not enough people

        $this->expectException(ImpossiblePlanningException::class);
        $assignement = $planner->makeAssignement($planning);

    }

    /**
     * @test
     */
    public function shouldProvideAValidAssignement(): void
    {
        $assignement = $this->generateTestAssignement();

        $validator = static::getContainer()->get('validator');
        $errors = $validator->validate($assignement, new AssignementConstraint);
        
        $this->assertCount(0, $errors);
    }

    /**
     * @test
     * Check that nobody has more than one more assigned task than anyone else
     */
    public function shouldPreventPeopleFromHavingTooManyTasks(): void
    {        
        $assignement = $this->generateTestAssignement();

        $tasksPerPerson = [
            'person 1' => 0,
            'person 2' => 0,
            'person 3' => 0,
            'person 4' => 0,
            'person 5' => 0,
            'person 6' => 0,
        ];
        foreach ($assignement->getTasks() as $task) {
            /** @var Task $task */
            $tasksPerPerson[$task->getAssignee()->__toString()]++;
        }

        $min = min($tasksPerPerson);
        $max = max($tasksPerPerson);
        
        $this->assertLessThanOrEqual(1, $max - $min);
    }
}
