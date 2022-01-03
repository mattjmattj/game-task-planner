<?php

namespace App\Backtracking\DomainReducer;

use App\Backtracking\Domain;

/**
 * The most basic domain reduction, linked to the very validity of an assignment :
 * one can only do one thing each game
 */
final class OneTaskPerGameDomainReducer implements DomainReducerInterface
{
    public function __invoke(Domain &$domain): bool
    {
        $planning = $domain->getPlanning();
        $changed = false;
        for ($game = 0 ; $game < $planning->getGameCount() ; ++$game) {
            foreach ($planning->getTaskTypes() as $type) {
                $slotDomain = $domain->getDomain($game, $type);
                if (count($slotDomain) === 1) {
                    // here, we are certain that the only person in the domain of this slot
                    // cannot be in any other domain for the other slots of the same game
                    $person = reset($slotDomain);
                    foreach ($planning->getTaskTypes() as $t) {
                        if ($t === $type) {
                            continue;
                        }
                        $tDomain = $domain->getDomain($game, $t);
                        if (false !== ($k = array_search($person, $tDomain, true))) {
                            $changed = true;
                            unset($tDomain[$k]);
                            $domain->setDomain($game, $t, array_values($tDomain));
                        }
                    }
                }
            }
        }

        return $changed;
    }
}