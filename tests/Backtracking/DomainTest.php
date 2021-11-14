<?php

namespace App\Tests\Backtracking;

use App\Backtracking\Domain;
use App\Tests\Service\Planner\PlannerTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DomainTest extends KernelTestCase
{
    use PlannerTestTrait;

    /**
     * @test
     */
    public function canBeCopied()
    {
        $planning = $this->makePlanning();

        $domain = new Domain($planning);
        $copy = Domain::createCopy($domain);

        $this->assertEqualsCanonicalizing($domain, $copy);

        $type = $planning->getTaskTypes()->get(0);

        $copy->setDomain(0, $type, [$planning->getPersons()->get(1)]);

        $this->assertNotEqualsCanonicalizing($domain->getDomain(0, $type), $copy->getDomain(0, $type));
    }

    
}
