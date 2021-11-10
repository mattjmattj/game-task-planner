<?php

namespace App\Tests\Backtracking;

use App\Backtracking\BacktrackableAssignment;
use App\Tests\Service\Planner\PlannerTestTrait;
use PHPUnit\Framework\TestCase;

class BacktrackableAssignmentTest extends TestCase
{
    use PlannerTestTrait;

    /**
     * @test
     */
    public function shouldComputeEqualityBetweenAssignments(): void
    {
        $planning = $this->makePlanning();

        $ba1 = new BacktrackableAssignment($planning);
        $ba2 = new BacktrackableAssignment($planning);

        $this->assertTrue($ba1->equals($ba2));
        $this->assertTrue($ba2->equals($ba1));

        $ba1->setTask(0,$planning->getTaskTypes()->get(1), $planning->getPersons()->get(2));

        $this->assertFalse($ba1->equals($ba2));
        $this->assertFalse($ba2->equals($ba1));

        $ba2->setTask(0,$planning->getTaskTypes()->get(1), $planning->getPersons()->get(2));

        $this->assertTrue($ba1->equals($ba2));
        $this->assertTrue($ba2->equals($ba1));
    }
}
