<?php

namespace App\Backtracking\DomainReducer;

use App\Backtracking\Domain;

/**
 * A DomainReducer aims at computing a new, smaller domain 
 */
interface DomainReducerInterface
{
    /**
     * @return bool - true only if the domain has changed
     */
    public function __invoke(Domain &$domain): bool;
}