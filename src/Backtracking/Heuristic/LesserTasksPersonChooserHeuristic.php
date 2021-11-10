<?php

namespace App\Backtracking\Heuristic;

use App\Backtracking\BacktrackableAssignment;
use App\Entity\Person;
use App\Entity\TaskType;

final class LesserTasksPersonChooserHeuristic implements PersonChooserHeuristicInterface
{
    public function __invoke(BacktrackableAssignment $ba, int $game, TaskType $type): iterable
    {
        $persons = $ba->getAvailablePersons($game, $type);
        usort($persons, fn(Person $a, Person $b) => $ba->getTaskCount($a) <=> $ba->getTaskCount($b));
        foreach ($persons as $person) {
            yield $person;
        }
    }
}