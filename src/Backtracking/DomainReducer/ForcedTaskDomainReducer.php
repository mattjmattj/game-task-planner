<?php

namespace App\Backtracking\DomainReducer;

use App\Backtracking\Domain;

/**
 * To be used with ForcedTaskConstraint : reduces the domain according to 
 * every registered forced tasks
 */
final class ForcedTaskDomainReducer implements DomainReducerInterface
{
    public function __invoke(Domain &$domain): bool
    {
        $planning = $domain->getPlanning();
        $changed = false;
        
        foreach ($planning->getForcedTasks() as $forcedTask) {
            $expectedDomain = [$forcedTask->getPerson()];
            $actualDomain = $domain->getDomain($forcedTask->getGame(), $forcedTask->getTaskType());

            if ($expectedDomain !== $actualDomain) {
                $domain->setDomain(
                    $forcedTask->getGame(),
                    $forcedTask->getTaskType(),
                    [$forcedTask->getPerson()]
                );
                $changed = true;
            }
        }

        return $changed;
    }
}