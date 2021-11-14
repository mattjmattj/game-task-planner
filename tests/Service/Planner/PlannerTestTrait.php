<?php

namespace App\Tests\Service\Planner;

use App\Assignment\Assignment;
use App\Entity\Person;
use App\Entity\Planning;
use App\Entity\TaskType;
use App\Service\Planner\PlannerInterface;

trait PlannerTestTrait
{
    public function makePlanning(int $nbGames = 3, int $nbPersons = 6, int $nbTaskTypes = 4): Planning
    {
        $planning = new Planning;

        $planning->setTitle('Test planning');
        $planning->setGameCount($nbGames);
        for ($i = 0 ; $i < $nbTaskTypes ; ++$i) {
            $planning->addTaskType((new TaskType)->setName("T$i"));
        }
        for ($i = 0 ; $i < $nbPersons ; ++$i) {
            $planning->addPerson((new Person)->setName("P$i"));
        }

        return $planning;
    }

    /**
     * Shortcut method for generating an assignment
     */
    public function generateTestAssignment(PlannerInterface $planner, Planning $planning): Assignment
    {
        $assignment = $planner->makeAssignment($planning);

        $this->assertInstanceOf(Assignment::class, $assignment);    
        $this->assertEquals($planning, $assignment->getPlanning());
        
        return $assignment;
    }
}