<?php

namespace App\Backtracking\Heuristic;

use App\Backtracking\BacktrackableAssignment;

interface TaskSlotChooserHeuristicInterface
{
    /**
     * @return [int, TaskType]
     * @throws \Exception when there is no task slot available
     */
    public function __invoke(BacktrackableAssignment $ba): array;
}