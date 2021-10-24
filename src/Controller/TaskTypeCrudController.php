<?php

namespace App\Controller;

use App\Entity\TaskType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class TaskTypeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TaskType::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
