<?php

namespace App\Tests\Service;

use App\Service\AssignementGenerator;
use App\Tests\Service\Planner\PlannerTestTrait;
use App\Validator\Assignement as AssignementConstraint;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AssignementGeneratorTest extends KernelTestCase
{
    use PlannerTestTrait;

    /**
     * @test
     */
    public function shouldGenerateEveryValidAssignementForAPlanning(): void
    {
        $generator = new AssignementGenerator;

        $planning = $this->makePlanning(1, 2, 2);
        $assignements = iterator_to_array($generator->assignements($planning));

        $this->assertCount(2, $assignements);

        $validator = static::getContainer()->get('validator');
        foreach ($assignements as $assignement) {
            $errors = $validator->validate($assignement, new AssignementConstraint);
            $this->assertCount(0, $errors);
        }
    }
}
