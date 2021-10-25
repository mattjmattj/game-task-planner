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

        if (0 === count($types)) {
            return $assignement;
        }

        // we'll use a priority queue initialized with every person having a priority
        // equal to the number of games. Every time a task is assigned to somebody
        // their priority is decreased, making them the last pick
        $peopleQueue = new \SplPriorityQueue;
        $peopleQueue->setExtractFlags(\SplPriorityQueue::EXTR_BOTH);
        foreach ($persons as $person) {
            $peopleQueue->insert($person, $nbGames);
        }
        
        for ($game = 0 ; $game < $nbGames ; $game++) {
            // for every game, we have to keep track of who had a task assigned
            // and put them back in the queue only once all the tasks have been
            // assigned for the game
            $selectedPeople = [];
            foreach($types as $type) {
                $queueTop = $peopleQueue->extract();
                $assignee = $queueTop['data'];
                $priority = $queueTop['priority'];

                $assignement->addTask(
                    (new Task)
                        ->setAssignee($assignee)
                        ->setType($type)
                        ->setGame($game)
                );

                $selectedPeople[] = [$assignee, $priority - 1];
            }

            foreach($selectedPeople as list($person, $priority)) {
                $peopleQueue->insert($person, $priority);
            }
        }

        return $assignement;
    }
}