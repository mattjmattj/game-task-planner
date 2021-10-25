<?php

namespace App\Service\Planner;

use App\Entity\Assignement;
use App\Entity\Planning;

interface PlannerInterface
{
    public function makeAssignement(Planning $planning): Assignement;
}