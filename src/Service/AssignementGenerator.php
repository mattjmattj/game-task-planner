<?php

namespace App\Service;

use App\Entity\Assignement;
use App\Entity\Planning;

final class AssignementGenerator
{
    public function assignements(Planning $planning): iterable
    {
        yield new Assignement;
    }
}