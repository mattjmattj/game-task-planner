<?php

namespace App\Service\Math;


final class CombinationGenerator
{

    /**
     * Recursively generates all k-combinations (unordered samples) of a list
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

    /**
     * Recursively generates all the permutations of a list
     */
    public function permutations(array $list): iterable
    {
        if (empty($list)) {
            yield [];
            return;
        }
        foreach ($list as $k => $element) {
            $copy = $list;
            array_splice($copy, $k, 1);
            foreach ($this->permutations($copy) as $rest) {
                yield [$element, ...$rest];
            }
        }
    }
}