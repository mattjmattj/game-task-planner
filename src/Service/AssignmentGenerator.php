<?php

namespace App\Service;

use App\Entity\Assignment;
use App\Entity\Planning;

final class AssignmentGenerator
{
    public function assignments(Planning $planning): iterable
    {
        yield new Assignment;
    }
}