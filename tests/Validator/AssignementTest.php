<?php

namespace App\Tests\Validator;

use App\Entity\Assignement;
use App\Entity\Person;
use App\Entity\Planning;
use App\Entity\TaskType;
use PHPUnit\Framework\TestCase;

final class AssignementTest extends TestCase
{
    private function makePlanning(): Planning
    {
        $planning = new Planning;

        $planning->addTaskType((new TaskType)->setName('type 1'));
        $planning->addTaskType((new TaskType)->setName('type 2'));
        $planning->addTaskType((new TaskType)->setName('type 3'));
        $planning->addPerson((new Person)->setName('person 1'));
        $planning->addPerson((new Person)->setName('person 2'));

        return $planning;
    }

    private function makeAssignement(Planning $planning): Assignement
    {
        $planning = $this->makePlanning();

        $assignement = new Assignement;
        $assignement->setPlanning($planning);

        return $assignement;
    }

    /**
     * @test
     */
    public function shouldValidateAPerfectAssignement(): void
    {        
        $this->markTestIncomplete();
    }

    /**
     * @test
     */
    public function shouldRaiseAnErrorWhenSomeTasksAreMissing(): void
    {
        $this->markTestIncomplete();
    }

    /**
     * @test
     */
    public function shouldRaiseAnErrorWhenSomeTasksAreDuplicated(): void
    {
        $this->markTestIncomplete();
    }

    /**
     * @test
     */
    public function shouldRaiseAnErrorWhenSomePeopleAreGivenMultipleTasksForTheSameGame(): void
    {
        $this->markTestIncomplete();
    }

}
