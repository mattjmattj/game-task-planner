<?php

namespace App\Tests\Service;

use App\Service\AssignmentGenerator;
use App\Tests\Service\Planner\PlannerTestTrait;
use App\Validator\Assignment as AssignmentConstraint;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AssignmentGeneratorTest extends KernelTestCase
{
    use PlannerTestTrait;

    /**
     * @test
     */
    public function shouldGenerateEveryValidAssignmentForAPlanning(): void
    {
        $generator = new AssignmentGenerator;

        $planning = $this->makePlanning(1, 2, 2);
        $assignments = iterator_to_array($generator->assignments($planning));

        $this->assertCount(2, $assignments);

        $validator = static::getContainer()->get('validator');
        foreach ($assignments as $assignment) {
            $errors = $validator->validate($assignment, new AssignmentConstraint);
            $this->assertCount(0, $errors);
        }
    }
}
