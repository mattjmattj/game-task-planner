<?php

namespace App\Backtracking;

use App\Entity\Person;
use App\Entity\Planning;
use App\Entity\TaskType;

final class Domain
{
    /**
     * array -> \SplObjectStorage -> array
     * game  -> type              -> people
     */
    private array $domain;

    public function __construct(
        private Planning $planning
    )
    {
        $this->initDomain();
    }

    public static function createCopy(Domain $original): self
    {
        $planning = $original->planning;
        $new = new Domain($planning);
        
        for ($game = 0 ; $game < $planning->getGameCount() ; ++$game) {
            foreach ($planning->getTaskTypes() as $type) {
                $new->setDomain($game, $type, $original->getDomain($game, $type));
            }
        }

        return $new;
    }

    private function initDomain()
    {
        for ($game = 0 ; $game < $this->planning->getGameCount() ; ++$game) {
            $this->domain[$game] = new \SplObjectStorage;
            foreach ($this->planning->getTaskTypes() as $type) {
                $this->setDomain($game, $type, $this->planning->getPersons()->toArray());
            }
        }
    }

    /**
     * @return Person[]
     */
    public function getDomain(int $game, TaskType $type): array
    {
        return $this->domain[$game][$type];
    }

    public function getDomainCount(int $game, TaskType $type): int
    {
        return count($this->domain[$game][$type]);
    }

    public function setDomain(int $game, TaskType $type, array $people): self
    {
        $this->domain[$game][$type] = array_values($people);
        return $this;
    }

    public function getPlanning(): Planning
    {
        return $this->planning;
    }
}