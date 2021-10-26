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

    /**
     * @test
     * Check that nobody is given more of a specific type of task than anyone else
     */
    public function shouldPreventAssigningTheSameTaskTypeToTheSamePeople(): void
    {
        $this->markTestSkipped('Not implemented yet');
        $assignement = $this->generateTestAssignement($this->planner, $this->makePlanning());

        $details = [
            'type 1' => [
                'person 1' => 0,
                'person 2' => 0,
                'person 3' => 0,
                'person 4' => 0,
                'person 5' => 0,
                'person 6' => 0,
            ],
            'type 2' => [
                'person 1' => 0,
                'person 2' => 0,
                'person 3' => 0,
                'person 4' => 0,
                'person 5' => 0,
                'person 6' => 0,
            ],
            'type 3' => [
                'person 1' => 0,
                'person 2' => 0,
                'person 3' => 0,
                'person 4' => 0,
                'person 5' => 0,
                'person 6' => 0,
            ],
            'type 4' => [
                'person 1' => 0,
                'person 2' => 0,
                'person 3' => 0,
                'person 4' => 0,
                'person 5' => 0,
                'person 6' => 0,
            ],
        ];

        foreach ($assignement->getTasks() as $task) {
            /** @var Task $task */
            $details[$task->getType()->__toString()][$task->getAssignee()->__toString()]++;
        }

        foreach ($details as $type => $tasksPerPerson) {
            $min = min($tasksPerPerson);
            $max = max($tasksPerPerson);
        
            $this->assertLessThanOrEqual(1, $max - $min);
        }
    }
}