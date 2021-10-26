<?php

namespace App\Tests\Service\Planner;

use App\Entity\Assignement;
use App\Entity\Person;
use App\Entity\Planning;
use App\Entity\TaskType;
use App\Service\Planner\PlannerInterface;

trait PlannerTestTrait
{
    public function makePlanning(int $nbGames = 6, int $nbPersons = 6, int $nbTaskTypes = 4): Planning
    {
        $planning = new Planning;

        $planning->setTitle('Test planning');
        $planning->setGameCount($nbGames);
        for ($i = 1 ; $i <= $nbTaskTypes ; ++$i) {
            $planning->addTaskType((new TaskType)->setName("type $i"));
        }
        for ($i = 1 ; $i <= $nbPersons ; ++$i) {
            $planning->addPerson((new Person)->setName("person $i"));
        }

        return $planning;
    }

    /**
     * Shortcut method for generating an assignement
     */
    public function generateTestAssignement(PlannerInterface $planner, Planning $planning): Assignement
    {
        $assignement = $planner->makeAssignement($planning);

        $this->assertInstanceOf(Assignement::class, $assignement);    
        $this->assertEquals($planning, $assignement->getPlanning());
        
        return $assignement;
    }
}