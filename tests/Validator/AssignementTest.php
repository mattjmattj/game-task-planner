<?php

namespace App\Tests\Validator;

use App\Entity\Assignement;
use App\Entity\Person;
use App\Entity\Planning;
use App\Entity\Task;
use App\Entity\TaskType;
use App\Validator\Assignement as AssignementConstraint;
use App\Validator\AssignementValidator as AssignementValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

final class AssignementTest extends ConstraintValidatorTestCase
{
    public function createValidator()
    {
        return new AssignementValidator();
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

    private function makeAssignement(Planning $planning): Assignement
    {
        $planning = $this->makePlanning();

        $assignement = new Assignement;
        $assignement->setPlanning($planning);

        return $assignement;
    }

    /**
     * @test
     */
    public function shouldValidateAPerfectAssignement(): void
    {
        $planning = $this->makePlanning();
        $persons = $planning->getPersons();
        $taskTypes = $planning->getTaskTypes();
        $assignement = $this->makeAssignement($planning);

        $assignement->addTask(
            (new Task)->setAssignee($persons[0])->setType($taskTypes[0])->setGame(0)
        );
        $assignement->addTask(
            (new Task)->setAssignee($persons[1])->setType($taskTypes[1])->setGame(0)
        );
        $assignement->addTask(
            (new Task)->setAssignee($persons[1])->setType($taskTypes[0])->setGame(1)
        );
        $assignement->addTask(
            (new Task)->setAssignee($persons[2])->setType($taskTypes[1])->setGame(1)
        );
        $assignement->addTask(
            (new Task)->setAssignee($persons[2])->setType($taskTypes[0])->setGame(2)
        );
        $assignement->addTask(
            (new Task)->setAssignee($persons[0])->setType($taskTypes[1])->setGame(2)
        );

        $this->validator->validate($assignement, new AssignementConstraint);
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
        $assignement = $this->makeAssignement($planning);

        $assignement->addTask(
            (new Task)->setAssignee($persons[0])->setType($taskTypes[0])->setGame(0)
        );
        $assignement->addTask(
            (new Task)->setAssignee($persons[1])->setType($taskTypes[1])->setGame(0)
        );
        $assignement->addTask(
            (new Task)->setAssignee($persons[1])->setType($taskTypes[0])->setGame(1)
        );
        $assignement->addTask(
            (new Task)->setAssignee($persons[2])->setType($taskTypes[1])->setGame(1)
        );
        // removing one task from previous test
        $assignement->addTask(
            (new Task)->setAssignee($persons[0])->setType($taskTypes[1])->setGame(2)
        );

        $constraint = new AssignementConstraint;
        $this->validator->validate($assignement, $constraint);

        $this->buildViolation($constraint->message)
            ->setCode(AssignementConstraint::MISSING_TASKS_ERROR)
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
        $assignement = $this->makeAssignement($planning);

        $assignement->addTask(
            (new Task)->setAssignee($persons[0])->setType($taskTypes[0])->setGame(0)
        );
        $assignement->addTask(
            (new Task)->setAssignee($persons[1])->setType($taskTypes[1])->setGame(0)
        );
        $assignement->addTask(
            (new Task)->setAssignee($persons[1])->setType($taskTypes[0])->setGame(1)
        );
        $assignement->addTask(
            (new Task)->setAssignee($persons[2])->setType($taskTypes[1])->setGame(1)
        );
        $assignement->addTask(
            (new Task)->setAssignee($persons[2])->setType($taskTypes[0])->setGame(2)
        );
        // second type[0] task for game 2
        $assignement->addTask(
            (new Task)->setAssignee($persons[0])->setType($taskTypes[0])->setGame(2)
        );

        $constraint = new AssignementConstraint;
        $this->validator->validate($assignement, $constraint);

        $this->buildViolation($constraint->message)
            ->setCode(AssignementConstraint::DUPLICATED_TASKS_ERROR)
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
        $assignement = $this->makeAssignement($planning);

        $assignement->addTask(
            (new Task)->setAssignee($persons[0])->setType($taskTypes[0])->setGame(0)
        );
        $assignement->addTask(
            (new Task)->setAssignee($persons[1])->setType($taskTypes[1])->setGame(0)
        );
        $assignement->addTask(
            (new Task)->setAssignee($persons[1])->setType($taskTypes[0])->setGame(1)
        );
        // person 1 will do both tasks
        $assignement->addTask(
            (new Task)->setAssignee($persons[1])->setType($taskTypes[1])->setGame(1)
        );
        $assignement->addTask(
            (new Task)->setAssignee($persons[2])->setType($taskTypes[0])->setGame(2)
        );
        $assignement->addTask(
            (new Task)->setAssignee($persons[0])->setType($taskTypes[1])->setGame(2)
        );

        $constraint = new AssignementConstraint;
        $this->validator->validate($assignement, $constraint);

        $this->buildViolation($constraint->message)
            ->setCode(AssignementConstraint::MULTIPLE_TASKS_ERROR)
            ->assertRaised();
    }

}
