<?php

namespace App\Backtracking\Heuristic;

use App\Backtracking\BacktrackableAssignment;
use App\Entity\TaskType;

final class NullPersonChooserHeuristic implements PersonChooserHeuristicInterface
{
    public function __invoke(BacktrackableAssignment $ba, int $game, TaskType $type): iterable
    {
        yield from $ba->getAvailablePersons($game, $type);
    }
}