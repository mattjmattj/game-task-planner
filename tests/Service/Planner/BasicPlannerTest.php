<?php

namespace App\Tests\Service\Planner;

use App\Service\Planner\BasicPlanner;
use App\Service\Planner\PlannerInterface;

/**
 * A BasicPlanner does the bare minimum
 */
class BasicPlannerTest extends AbstractPlannerTest
{
    private BasicPlanner $planner;

    public function setUp(): void
    {
        $this->planner =new BasicPlanner;
    }

    public function getPlanner(): PlannerInterface
    {
        return $this->planner;
    }

    /**
     * @test
     */
    public function isCorrectlyConfiguredAsAService(): void
    {
        $kernel = self::bootKernel();

        $this->assertSame('test', $kernel->getEnvironment());

        $planner = static::getContainer()->get(BasicPlanner::class);
        $this->assertInstanceOf(PlannerInterface::class, $planner);
    }
}