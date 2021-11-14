<?php

namespace App\Backtracking\DomainReducer;

use App\Backtracking\Domain;

/**
 * To be used with the NotTwiceTheSameTaskConstraint : makes sure someone cannot
 * be assigned the same task twice in a row
 */
final class NotTwiceTheSameTaskDomainReducer implements DomainReducerInterface
{
    public function __invoke(Domain &$domain): bool
    {
        $planning = $domain->getPlanning();
        $changed = false;
        foreach ($planning->getTaskTypes() as $type) {
            do {
                $peopleRemoved = false;
                // loop stop condition is gameCount - 1, since the last game doesn't have a next game
                for ($game = 0; $game < $planning->getGameCount() - 1; ++$game) {
                    $slotDomain = $domain->getDomain($game, $type);
                    if (count($slotDomain) === 1) {
                        // here, we are certain that the only person in the domain of this slot
                        // cannot be assigned to the same task next game
                        $person = reset($slotDomain);
                        $tDomain = $domain->getDomain($game + 1, $type);                        
                        if (false !== ($k = array_search($person, $tDomain, true))) {
                            $changed = true;
                            $peopleRemoved = true;
                            unset($tDomain[$k]);
                            $domain->setDomain($game + 1, $type, array_values($tDomain));
                        }
                    }
                }
            } while ($peopleRemoved);
        }

        return $changed;
    }
}
