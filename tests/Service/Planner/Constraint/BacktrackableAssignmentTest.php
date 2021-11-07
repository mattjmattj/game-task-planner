<?php

namespace App\Tests\Service\Planner\Constraint;

use App\Backtracking\BacktrackableAssignment;
use App\Tests\Service\Planner\PlannerTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BacktrackableAssignmentTest extends KernelTestCase
{
    use PlannerTestTrait;

    /**
     * @test
     */
    public function shouldManageAListOfAvailableTaskSlots()
    {
        $planning = $this->makePlanning(3, 6, 4);
        $ba = new BacktrackableAssignment($planning);

        $this->assertCount(12, $ba->getAvailableTaskSlots());

        $ba->setTask(1, $planning->getTaskTypes()->get(1), $planning->getPersons()->get(2));

        $this->assertCount(11, $ba->getAvailableTaskSlots());
        $this->assertFalse($ba->isTaskSlotAvailable(1, $planning->getTaskTypes()->get(1)));
        $this->assertTrue($ba->isTaskSlotAvailable(2, $planning->getTaskTypes()->get(1)));
        $this->assertTrue($ba->isTaskSlotAvailable(1, $planning->getTaskTypes()->get(0)));

        $ba->unsetTask(1, $planning->getTaskTypes()->get(1));

        $this->assertCount(12, $ba->getAvailableTaskSlots());
    }

    /**
     * @test
     */
    public function shouldConvertToAnAssignment()
    {
        $planning = $this->makePlanning(2, 5, 4);
        $ba = new BacktrackableAssignment($planning);

        $ba->setTask(0, $planning->getTaskTypes()->get(0), $planning->getPersons()->get(1));
        $ba->setTask(0, $planning->getTaskTypes()->get(1), $planning->getPersons()->get(2));
        $ba->setTask(0, $planning->getTaskTypes()->get(2), $planning->getPersons()->get(3));
        $ba->setTask(0, $planning->getTaskTypes()->get(3), $planning->getPersons()->get(4));

        $ba->setTask(1, $planning->getTaskTypes()->get(0), $planning->getPersons()->get(0));
        $ba->setTask(1, $planning->getTaskTypes()->get(1), $planning->getPersons()->get(1));
        $ba->setTask(1, $planning->getTaskTypes()->get(2), $planning->getPersons()->get(2));
        $ba->setTask(1, $planning->getTaskTypes()->get(3), $planning->getPersons()->get(3));

        $this->assertCount(0, $ba->getAvailableTaskSlots());

        $validator = static::getContainer()->get('validator');
        $errors = $validator->validate($ba->makeAssignment());
        
        $this->assertCount(0, $errors);
    }

    /**
     * @test
     */
    public function shouldManageAListOfAvailablePersonsForATaskSlot()
    {
        $planning = $this->makePlanning(3, 6, 4);
        $ba = new BacktrackableAssignment($planning);

        $ba->setTask(0, $planning->getTaskTypes()->get(0), $planning->getPersons()->get(1));
        $ba->setTask(0, $planning->getTaskTypes()->get(1), $planning->getPersons()->get(2));
        $ba->setTask(0, $planning->getTaskTypes()->get(2), $planning->getPersons()->get(3));

        $availablePersons = $ba->getAvailablePersons(0, $planning->getTaskTypes()->get(3));

        $this->assertEqualsCanonicalizing([
            $planning->getPersons()->get(0),
            $planning->getPersons()->get(4),
            $planning->getPersons()->get(5)
        ], $availablePersons);
    }

    /**
     * @test
     */
    public function shouldKeepTrackOfATaskCountPerPerson()
    {
        $planning = $this->makePlanning(3, 6, 4);
        $ba = new BacktrackableAssignment($planning);

        $ba->setTask(0, $planning->getTaskTypes()->get(0), $planning->getPersons()->get(1));
        $ba->setTask(0, $planning->getTaskTypes()->get(1), $planning->getPersons()->get(2));
        $ba->setTask(0, $planning->getTaskTypes()->get(2), $planning->getPersons()->get(3));
        $ba->setTask(1, $planning->getTaskTypes()->get(2), $planning->getPersons()->get(3));

        $this->assertEquals(0, $ba->getTaskCount($planning->getPersons()->get(0)));
        $this->assertEquals(1, $ba->getTaskCount($planning->getPersons()->get(1)));
        $this->assertEquals(1, $ba->getTaskCount($planning->getPersons()->get(2)));
        $this->assertEquals(2, $ba->getTaskCount($planning->getPersons()->get(3)));
        $this->assertEquals(0, $ba->getTaskCount($planning->getPersons()->get(4)));
        $this->assertEquals(0, $ba->getTaskCount($planning->getPersons()->get(5)));
    }
}
