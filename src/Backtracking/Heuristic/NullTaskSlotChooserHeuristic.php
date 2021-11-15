<?php

namespace App\Backtracking\Heuristic;

use App\Backtracking\BacktrackableAssignment;

final class NullTaskSlotChooserHeuristic implements TaskSlotChooserHeuristicInterface
{
    public function __invoke(BacktrackableAssignment $ba): array
    {
        $slots = $ba->getAvailableTaskSlots();
        if (empty($slots)) {
            throw new \Exception('No slot available');
        }
        return array_pop($slots);
    }
}