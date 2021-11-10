<?php

namespace App\Backtracking\Heuristic;

use App\Backtracking\BacktrackableAssignment;
use App\Entity\Person;
use App\Entity\TaskType;

final class NoSpecialistPersonChooserHeuristic implements PersonChooserHeuristicInterface
{
    public function __invoke(BacktrackableAssignment $ba, int $game, TaskType $type): iterable
    {
        $persons = $ba->getAvailablePersons($game, $type);
        $taskSlots = $ba->getTaskSlotsByType($type);

        usort($persons, function(Person $a, Person $b) use ($ba, $taskSlots) {
            $countA = $countB = 0;
            foreach ($taskSlots as $person) {
                if ($a === $person) {
                    $countA++;
                } elseif ($b === $person) {
                    $countB++;
                }
            }
            
            $cmp = $countA <=> $countB;
            if ($cmp !== 0) {
                return $cmp;
            }
            return $ba->getTaskCount($a) <=> $ba->getTaskCount($b);
        });

        foreach ($persons as $person) {
            yield $person;
        }
    }
}