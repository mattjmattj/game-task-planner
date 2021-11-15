<?php

namespace App\Tests\Backtracking\Constraint;

use App\Backtracking\BacktrackableAssignment;
use App\Backtracking\Constraint\NotTwiceTheSameTaskConstraint;
use App\Tests\Service\Planner\PlannerTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class NotTwiceTheSamePersonConstraintTest extends KernelTestCase
{
    use PlannerTestTrait;

    /**
     * @test
     */
    public function shouldForbidAssigningATaskToTheSamePersonTwiceInARow(): void
    {
        $constraint = new NotTwiceTheSameTaskConstraint;

        $planning = $this->makePlanning(3, 6, 4);
        $ba = new BacktrackableAssignment($planning);
        
        $this->assertFalse($constraint->reject($ba));
        $this->assertTrue($constraint->validate($ba));

        $ba->setTask(0, $planning->getTaskTypes()->get(1), $planning->getPersons()->get(2));

        $this->assertFalse($constraint->reject($ba));
        $this->assertTrue($constraint->validate($ba));

        $ba->setTask(0, $planning->getTaskTypes()->get(0), $planning->getPersons()->get(1));
        
        $this->assertFalse($constraint->reject($ba));
        $this->assertTrue($constraint->validate($ba));

        $ba->setTask(1, $planning->getTaskTypes()->get(1), $planning->getPersons()->get(2));
        
        $this->assertTrue($constraint->reject($ba));
        $this->assertFalse($constraint->validate($ba));

    }
}
