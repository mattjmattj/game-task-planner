<?php

namespace App\Service;

use App\Entity\Assignment;
use App\Entity\Planning;
use App\Entity\Task;
use App\Service\Math\CombinationGenerator;

final class AssignmentGenerator
{
    public function __construct(
        private CombinationGenerator $combinationGenerator
    )
    {}
    
    /**
     * Will generate all valid Assignment for a given Planning
     */
    public function assignments(Planning $planning): iterable
    {
        foreach ($this->makeLines($planning, $planning->getGameCount()) as $lines) {
            $assignment = new Assignment;
            $assignment->setPlanning($planning);

            foreach ($lines as $game => $line) {
                foreach ($line as $typeRef => $person) {
                    $assignment->addTask(
                        (new Task)
                            ->setAssignee($person)
                            ->setType($planning->getTaskTypes()->get($typeRef))
                            ->setGame($game)
                    );
                }
            }

            yield $assignment;
        }
    }

    private function makeLines(Planning $planning, int $gameCount)
    {
        if (0 === $gameCount) {
            yield [];
            return;
        }

        $persons = $planning->getPersons()->toArray();
        $k = $planning->getTaskTypes()->count();
        foreach ($this->combinationGenerator->kCombinations($persons, $k) as $combination) {
            foreach ($this->combinationGenerator->permutations($combination) as $line) {
                foreach ($this->makeLines($planning, $gameCount - 1) as $remainingLines) {
                    yield [$line, ...$remainingLines];
                }
            }
        }
    }

    

}