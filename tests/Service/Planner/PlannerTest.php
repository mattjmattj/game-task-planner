<?php

namespace App\Tests\Service\Planner;

use App\Service\Planner\PlannerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PlannerTest extends KernelTestCase
{
    /**
     * @test
     */
    public function isCorrectlyConfiguredAsAService(): void
    {
        $kernel = self::bootKernel();

        $this->assertSame('test', $kernel->getEnvironment());

        $planner = static::getContainer()->get(PlannerInterface::class);
        $this->assertInstanceOf(PlannerInterface::class, $planner);
    }
}
