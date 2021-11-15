<?php

namespace App\Tests\Backtracking\Constraint;

use App\Backtracking\BacktrackableAssignment;
use App\Backtracking\Constraint\NoSpecialistConstraint;
use App\Tests\Service\Planner\PlannerTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class NoSpecialistConstraintTest extends KernelTestCase
{
    use PlannerTestTrait;

    /**
     * @test
     */
    public function shouldForbidAssigningATaskTooMuchToTheSamePerson(): void
    {
        $constraint = new NoSpecialistConstraint;

        $planning = $this->makePlanning(3, 6, 4);
        $ba = new BacktrackableAssignment($planning);
        
        // 0 everywhere
        $this->assertTrue($constraint->validate($ba));

        $ba->setTask(0, $planning->getTaskTypes()->get(1), $planning->getPersons()->get(2));

        // 1 for one, 0 elsewhere
        $this->assertTrue($constraint->validate($ba));

        $ba->setTask(1, $planning->getTaskTypes()->get(1), $planning->getPersons()->get(2));
        
        // 2 for one, 0 elsewhere : no good
        $this->assertFalse($constraint->validate($ba));

        $ba->setTask(2, $planning->getTaskTypes()->get(1), $planning->getPersons()->get(0));
        
        // 2, 1 and 0 : no good
        $this->assertFalse($constraint->validate($ba));
        
        // 1, 1, 1 and 0s : good again
        $ba->setTask(1, $planning->getTaskTypes()->get(1), $planning->getPersons()->get(1));

        $this->assertTrue($constraint->validate($ba));
    }

    /**
     * @test
     * BUG : This assignment should not be accepted by the NoSpecialistConstraint
     *  #	A	B	C	D
     *  #0	0	1	2	3
     *  #1	0	1	3	2
     *  #2	0	3	1	2
     *  #3	3	0	1	2
     */
    public function bug1()
    {
        $constraint = new NoSpecialistConstraint;

        $planning = $this->makePlanning(4, 4, 4);
        $ba = new BacktrackableAssignment($planning);

        $p = $planning->getPersons();
        $t = $planning->getTaskTypes();

        $ba->setTask(0, $t[0], $p[0]);
        $ba->setTask(0, $t[1], $p[1]);
        $ba->setTask(0, $t[2], $p[2]);
        $ba->setTask(0, $t[3], $p[3]);

        $ba->setTask(1, $t[0], $p[0]);
        $ba->setTask(1, $t[1], $p[1]);
        $ba->setTask(1, $t[2], $p[3]);
        $ba->setTask(1, $t[3], $p[2]);

        $ba->setTask(2, $t[0], $p[0]);
        $ba->setTask(2, $t[1], $p[3]);
        $ba->setTask(2, $t[2], $p[1]);
        $ba->setTask(2, $t[3], $p[2]);

        $ba->setTask(3, $t[0], $p[3]);
        $ba->setTask(3, $t[1], $p[0]);
        $ba->setTask(3, $t[2], $p[1]);
        $ba->setTask(3, $t[3], $p[2]);

        $this->assertFalse($constraint->validate($ba));
    }
}
