<?php

namespace App\Service\Planner;

use App\Entity\Assignment;
use App\Entity\Planning;

interface PlannerInterface
{
    public function makeAssignment(Planning $planning): Assignment;
}