<?php

namespace App\Service\Planner;

use App\Entity\Assignement;
use App\Entity\Planning;
use App\Entity\Task;

final class BasicPlanner implements PlannerInterface
{
    public function makeAssignement(Planning $planning): Assignement
    {
        $assignement = new Assignement;

        $assignement->setPlanning($planning);

        $persons = $planning->getPersons();
        $types = $planning->getTaskTypes();
        $nbGames = $planning->getGameCount();

        if (count($persons) < count($types)) {
            throw new ImpossiblePlanningException("Not enough people for the given task types.");
        }

        // naive algorithm
        for ($game = 0 ; $game < $nbGames ; $game++) {
            foreach($types as $k => $type) {
                $assignement->addTask(
                    (new Task)
                        ->setAssignee($persons[$k])
                        ->setType($type)
                        ->setGame($game)
                );
            }
        }

        return $assignement;
    }
}