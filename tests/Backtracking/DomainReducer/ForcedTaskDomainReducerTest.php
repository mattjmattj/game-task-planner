<?php

namespace App\Tests\Backtracking\DomainReducer;

use App\Backtracking\Domain;
use App\Backtracking\DomainReducer\ForcedTaskDomainReducer;
use App\Entity\ForcedTask;
use App\Tests\Service\Planner\PlannerTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ForcedTaskDomainReducerTest extends KernelTestCase
{
    use PlannerTestTrait;

    /**
     * @test
     */
    public function shouldReduceDomainAccordingToTheNotTwiceTheSameTaskConstraint(): void
    {
        $planning = $this->makePlanning(3,4,4);
        $reducer = new ForcedTaskDomainReducer;

        $p0 = $planning->getPersons()->get(0);
        $p1 = $planning->getPersons()->get(1);
        $p2 = $planning->getPersons()->get(2);
        $p3 = $planning->getPersons()->get(3);

        $t0 = $planning->getTaskTypes()->get(0);
        $t1 = $planning->getTaskTypes()->get(1);
        $t2 = $planning->getTaskTypes()->get(2);
        $t3 = $planning->getTaskTypes()->get(3);

        $f1 = new ForcedTask();
        $f1->setPerson($p1)->setTaskType($t1)->setGame(1);
        $planning->addForcedTask($f1);
        
        $f2 = new ForcedTask();
        $f2->setPerson($p2)->setTaskType($t2)->setGame(1);
        $planning->addForcedTask($f2);
        
        $domain = new Domain($planning);

        $reducer($domain);

        $this->assertEqualsCanonicalizing([$p0, $p1, $p2, $p3], $domain->getDomain(0, $t0));
        $this->assertEqualsCanonicalizing([$p0, $p1, $p2, $p3], $domain->getDomain(0, $t1));
        $this->assertEqualsCanonicalizing([$p0, $p1, $p2, $p3], $domain->getDomain(0, $t2));
        $this->assertEqualsCanonicalizing([$p0, $p1, $p2, $p3], $domain->getDomain(0, $t3));
        $this->assertEqualsCanonicalizing([$p0, $p1, $p2, $p3], $domain->getDomain(1, $t0));
        $this->assertEqualsCanonicalizing([$p1], $domain->getDomain(1, $t1));
        $this->assertEqualsCanonicalizing([$p2], $domain->getDomain(1, $t2));
        $this->assertEqualsCanonicalizing([$p0, $p1, $p2, $p3], $domain->getDomain(1, $t3));
        $this->assertEqualsCanonicalizing([$p0, $p1, $p2, $p3], $domain->getDomain(2, $t0));
        $this->assertEqualsCanonicalizing([$p0, $p1, $p2, $p3], $domain->getDomain(2, $t1));
        $this->assertEqualsCanonicalizing([$p0, $p1, $p2, $p3], $domain->getDomain(2, $t2));
        $this->assertEqualsCanonicalizing([$p0, $p1, $p2, $p3], $domain->getDomain(2, $t3));
    }
}
