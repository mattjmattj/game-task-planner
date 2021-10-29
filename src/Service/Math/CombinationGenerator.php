<?php

namespace App\Service\Math;


final class CombinationGenerator
{

    /**
     * Recursively generating all k-combinations (unordered samples) of a list
     * Loosely inspired by https://rosettacode.org/wiki/Combinations#PHP
     */
    public function kCombinations(array $list, int $k): iterable
    {
        if (0 === $k) {
            yield [];
            return;
        }

        if (empty($list)) {
            return;
        }

        $head = array_shift($list);
        
        foreach ($this->kCombinations($list, $k - 1) as $rest) {
            yield [$head, ...$rest];
        }
    
        yield from $this->kCombinations($list, $k);
    }
}