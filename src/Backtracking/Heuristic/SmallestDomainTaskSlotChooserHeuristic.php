<?php

namespace App\Backtracking\Heuristic;

use App\Backtracking\BacktrackableAssignment;

final class SmallestDomainTaskSlotChooserHeuristic implements TaskSlotChooserHeuristicInterface
{
    public function __invoke(BacktrackableAssignment $ba): array
    {
        $slots = $ba->getAvailableTaskSlots();
        if (empty($slots)) {
            throw new \Exception('No slot available');
        }

        $count = fn($slot) => $ba->getAvailablePersonCount($slot[0], $slot[1]);

        usort(
            $slots,
            fn($a, $b) => $count($a) <=> $count($b)
        );

        return array_pop($slots);
    }
}