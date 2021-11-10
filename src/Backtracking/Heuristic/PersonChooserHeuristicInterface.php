<?php

namespace App\Backtracking\Heuristic;

use App\Backtracking\BacktrackableAssignment;
use App\Entity\TaskType;

interface PersonChooserHeuristicInterface
{
    public function __invoke(BacktrackableAssignment $ba, int $game, TaskType $type): iterable;
}