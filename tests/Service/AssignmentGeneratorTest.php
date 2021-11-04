<?php

namespace App\Tests\Service;

use App\Entity\Assignment;
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
        $validator = static::getContainer()->get('validator');

        // the number of possible assignments is P(2, 2)^1 = 2
        $planning = $this->makePlanning(1, 2, 2);
        $assignments = iterator_to_array($generator->assignments($planning), false);

        $this->assertCount(2, $assignments);

        foreach ($assignments as $assignment) {
            $errors = $validator->validate($assignment, new AssignmentConstraint);
            $this->assertCount(0, $errors);
        }

        // the number of possible assignments is P(3, 2)^1 = 6
        $planning = $this->makePlanning(1, 3, 2);
        $assignments = iterator_to_array($generator->assignments($planning), false);

        $this->assertCount(6, $assignments);

        foreach ($assignments as $assignment) {
            $errors = $validator->validate($assignment, new AssignmentConstraint);
            $this->assertCount(0, $errors);
        }


        // the number of possible assignments here is P(7, 4)^6 ~= 3e17. 
        // We're not gonna do that. Let's try the 1st thousand results
        $planning = $this->makePlanning(6, 7, 4);
        $n = 1000;
        $tabuList = [];
        $assignmentNotInTabu = function(Assignment $assignment) use ($tabuList) {
            foreach ($tabuList as $tabuAssignment) {
                if ($tabuAssignment->equals($assignment)) {
                    return false;
                }
            }
            return true;
        };
        foreach ($generator->assignments($planning) as $assignment) {
            $errors = $validator->validate($assignment, new AssignmentConstraint);
            $this->assertCount(0, $errors);

            $this->assertTrue($assignmentNotInTabu($assignment));
            $tabuList[] = $assignment;
            if (--$n < 0) {
                break;
            }
        }
    }
}
