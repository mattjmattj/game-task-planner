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
        $kernel = self::bootKernel();
        $this->assertSame('test', $kernel->getEnvironment());

        $generator = static::getContainer()->get(AssignmentGenerator::class);

        $planning = $this->makePlanning(1, 2, 2);
        $assignments = iterator_to_array($generator->assignments($planning), false);

        $this->assertCount(2, $assignments);

        $validator = static::getContainer()->get('validator');
        foreach ($assignments as $assignment) {
            $errors = $validator->validate($assignment, new AssignmentConstraint);
            $this->assertCount(0, $errors);
        }

        $planning = $this->makePlanning(1, 3, 2);
        $assignments = iterator_to_array($generator->assignments($planning), false);

        $this->assertCount(6, $assignments);

        $validator = static::getContainer()->get('validator');
        foreach ($assignments as $assignment) {
            $errors = $validator->validate($assignment, new AssignmentConstraint);
            $this->assertCount(0, $errors);
        }


        $this->markAsRisky();
        // FIXME current algorithm is terrible regarding performances. Cannot execute this case
        // $planning = $this->makePlanning(6, 7, 4);
        // $assignments = iterator_to_array($generator->assignments($planning), false);
        // $validator = static::getContainer()->get('validator');
        // foreach ($assignments as $assignment) {
        //     $errors = $validator->validate($assignment, new AssignmentConstraint);
        //     $this->assertCount(0, $errors);
        // }
    }
}
