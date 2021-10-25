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
        $planning->setGameCount(3);
        $planning->addTaskType((new TaskType)->setName('type 1'));
        $planning->addTaskType((new TaskType)->setName('type 2'));
        $planning->addPerson((new Person)->setName('person 1'));
        $planning->addPerson((new Person)->setName('person 2'));
        $planning->addPerson((new Person)->setName('person 3'));

        return $planning;
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
        $kernel = self::bootKernel();
        $this->assertSame('test', $kernel->getEnvironment());

        $planner = static::getContainer()->get(PlannerInterface::class);

        $planning = $this->makePlanning();

        $assignement = $planner->makeAssignement($planning);

        $this->assertInstanceOf(Assignement::class, $assignement);

        $this->assertEquals($planning, $assignement->getPlanning());

        $validator = static::getContainer()->get('validator');
        $errors = $validator->validate($assignement, new AssignementConstraint);
        
        $this->assertCount(0, $errors);
    }
}
