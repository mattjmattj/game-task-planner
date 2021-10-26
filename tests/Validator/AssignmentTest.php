<?php

namespace App\Tests\Validator;

use App\Entity\Assignment;
use App\Entity\Person;
use App\Entity\Planning;
use App\Entity\Task;
use App\Entity\TaskType;
use App\Validator\Assignment as AssignmentConstraint;
use App\Validator\AssignmentValidator as AssignmentValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

final class AssignmentTest extends ConstraintValidatorTestCase
{
    public function createValidator()
    {
        return new AssignmentValidator();
    }

    private function makePlanning(): Planning
    {
        $planning = new Planning;

        $planning->setTitle('Test planning');
        $planning->setGameCount(3);
        $planning->addTaskType((new TaskType)->setName('type 1'));
        $planning->addTaskType((new TaskType)->setName('type 2'));
        $planning->addPerson((new Person)->setName('person 1'));
        $planning->addPerson((new Person)->setName('person 2'));
        $planning->addPerson((new Person)->setName('person 3'));

        return $planning;
    }

    private function makeAssignment(Planning $planning): Assignment
    {
        $planning = $this->makePlanning();

        $assignment = new Assignment;
        $assignment->setPlanning($planning);

        return $assignment;
    }

    /**
     * @test
     */
    public function shouldValidateAPerfectAssignment(): void
    {
        $planning = $this->makePlanning();
        $persons = $planning->getPersons();
        $taskTypes = $planning->getTaskTypes();
        $assignment = $this->makeAssignment($planning);

        $assignment->addTask(
            (new Task)->setAssignee($persons[0])->setType($taskTypes[0])->setGame(0)
        );
        $assignment->addTask(
            (new Task)->setAssignee($persons[1])->setType($taskTypes[1])->setGame(0)
        );
        $assignment->addTask(
            (new Task)->setAssignee($persons[1])->setType($taskTypes[0])->setGame(1)
        );
        $assignment->addTask(
            (new Task)->setAssignee($persons[2])->setType($taskTypes[1])->setGame(1)
        );
        $assignment->addTask(
            (new Task)->setAssignee($persons[2])->setType($taskTypes[0])->setGame(2)
        );
        $assignment->addTask(
            (new Task)->setAssignee($persons[0])->setType($taskTypes[1])->setGame(2)
        );

        $this->validator->validate($assignment, new AssignmentConstraint);
        $this->assertNoViolation();
    }

    /**
     * @test
     */
    public function shouldNotValidateWhenSomeTasksAreMissing(): void
    {
        $this->expectNoValidate();

        $planning = $this->makePlanning();
        $persons = $planning->getPersons();
        $taskTypes = $planning->getTaskTypes();
        $assignment = $this->makeAssignment($planning);

        $assignment->addTask(
            (new Task)->setAssignee($persons[0])->setType($taskTypes[0])->setGame(0)
        );
        $assignment->addTask(
            (new Task)->setAssignee($persons[1])->setType($taskTypes[1])->setGame(0)
        );
        $assignment->addTask(
            (new Task)->setAssignee($persons[1])->setType($taskTypes[0])->setGame(1)
        );
        $assignment->addTask(
            (new Task)->setAssignee($persons[2])->setType($taskTypes[1])->setGame(1)
        );
        // removing one task from previous test
        $assignment->addTask(
            (new Task)->setAssignee($persons[0])->setType($taskTypes[1])->setGame(2)
        );

        $constraint = new AssignmentConstraint;
        $this->validator->validate($assignment, $constraint);

        $this->buildViolation($constraint->message)
            ->setCode(AssignmentConstraint::MISSING_TASKS_ERROR)
            ->assertRaised();
    }

    /**
     * @test
     */
    public function shouldNotValidateWhenSomeTasksAreDuplicated(): void
    {
        $planning = $this->makePlanning();
        $persons = $planning->getPersons();
        $taskTypes = $planning->getTaskTypes();
        $assignment = $this->makeAssignment($planning);

        $assignment->addTask(
            (new Task)->setAssignee($persons[0])->setType($taskTypes[0])->setGame(0)
        );
        $assignment->addTask(
            (new Task)->setAssignee($persons[1])->setType($taskTypes[1])->setGame(0)
        );
        $assignment->addTask(
            (new Task)->setAssignee($persons[1])->setType($taskTypes[0])->setGame(1)
        );
        $assignment->addTask(
            (new Task)->setAssignee($persons[2])->setType($taskTypes[1])->setGame(1)
        );
        $assignment->addTask(
            (new Task)->setAssignee($persons[2])->setType($taskTypes[0])->setGame(2)
        );
        // second type[0] task for game 2
        $assignment->addTask(
            (new Task)->setAssignee($persons[0])->setType($taskTypes[0])->setGame(2)
        );

        $constraint = new AssignmentConstraint;
        $this->validator->validate($assignment, $constraint);

        $this->buildViolation($constraint->message)
            ->setCode(AssignmentConstraint::DUPLICATED_TASKS_ERROR)
            ->assertRaised();
    }

    /**
     * @test
     */
    public function shouldNotValidateWhenSomePeopleAreGivenMultipleTasksForTheSameGame(): void
    {
        $planning = $this->makePlanning();
        $persons = $planning->getPersons();
        $taskTypes = $planning->getTaskTypes();
        $assignment = $this->makeAssignment($planning);

        $assignment->addTask(
            (new Task)->setAssignee($persons[0])->setType($taskTypes[0])->setGame(0)
        );
        $assignment->addTask(
            (new Task)->setAssignee($persons[1])->setType($taskTypes[1])->setGame(0)
        );
        $assignment->addTask(
            (new Task)->setAssignee($persons[1])->setType($taskTypes[0])->setGame(1)
        );
        // person 1 will do both tasks
        $assignment->addTask(
            (new Task)->setAssignee($persons[1])->setType($taskTypes[1])->setGame(1)
        );
        $assignment->addTask(
            (new Task)->setAssignee($persons[2])->setType($taskTypes[0])->setGame(2)
        );
        $assignment->addTask(
            (new Task)->setAssignee($persons[0])->setType($taskTypes[1])->setGame(2)
        );

        $constraint = new AssignmentConstraint;
        $this->validator->validate($assignment, $constraint);

        $this->buildViolation($constraint->message)
            ->setCode(AssignmentConstraint::MULTIPLE_TASKS_ERROR)
            ->assertRaised();
    }

}
