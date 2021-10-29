<?php

namespace App\Service;

use App\Entity\Assignment;
use App\Entity\Planning;
use App\Entity\Task;
use App\Service\Math\CombinationGenerator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class AssignmentGenerator
{
    public function __construct(
        private CombinationGenerator $combinationGenerator,
        private ValidatorInterface $validator
    )
    {}
    
    /**
     * Will generate all valid Assignment for a given Planning
     */
    public function assignments(Planning $planning): iterable
    {
        $persons = $planning->getPersons();
        $types = $planning->getTaskTypes();
        $nbGames = $planning->getGameCount();

        // dead-naive algorithm, highly complex, generating way too many invalid Assignments, consuming too much memory, etc.
        $allTasks = [];
        foreach ($persons as $person) {
            foreach ($types as $type) {
                for ($game = 0 ; $game < $nbGames ; ++$game) {
                    $allTasks[] = [$game, $type, $person];
                    // echo sprintf("%d,%s,%s\n",$game, $type->__toString(), $person->__toString());
                }
            }
        }

        $nbTasks = $types->count() * $nbGames;

        foreach ($this->combinationGenerator->kCombinations($allTasks, $nbTasks) as $combination) {
            $assignment = new Assignment;
            $assignment->setPlanning($planning);
            foreach ($combination as [$game, $type, $person]) {
                $assignment->addTask(
                    (new Task)
                        ->setGame($game)
                        ->setAssignee($person)
                        ->setType($type)
                );
            }

            if ($this->validator->validate($assignment)->count() === 0) {
                yield $assignment;
            }
        }
    }


}