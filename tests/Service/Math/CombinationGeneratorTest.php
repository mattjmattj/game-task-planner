<?php

namespace App\Tests\Service\Math;

use App\Service\Math\CombinationGenerator;
use PHPUnit\Framework\TestCase;

class CombinationGeneratorTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGenerateEveryKCombinationOfAGivenList(): void
    {
        $list = [1, 2, 3, 4, 5];

        $result = iterator_to_array((new CombinationGenerator)->kCombinations($list, 3), false);
        $this->assertEqualsCanonicalizing([
            [1, 2, 3],
            [1, 2, 4],
            [1, 2, 5],
            [1, 3, 4],
            [1, 3, 5],
            [1, 4, 5],
            [2, 3, 4],
            [2, 3, 5],
            [2, 4, 5],
            [3, 4, 5],
        ], $result);

        $result = iterator_to_array((new CombinationGenerator)->kCombinations($list, 2), false);
        $this->assertEqualsCanonicalizing([
            [1, 2],
            [1, 3],
            [1, 4],
            [1, 5],
            [2, 3],
            [2, 4],
            [2, 5],
            [3, 4],
            [3, 5],
            [4, 5],
        ], $result);

        $result = iterator_to_array((new CombinationGenerator)->kCombinations($list, 1), false);
        $this->assertEqualsCanonicalizing([
            [1],
            [2],
            [3],
            [4],
            [5],
        ], $result);
    }
}
