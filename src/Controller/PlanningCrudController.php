<?php

namespace App\Controller;

use App\Backtracking\Constraint\AssignmentValidatorConstraint;
use App\Backtracking\Constraint\NoSpecialistConstraint;
use App\Backtracking\Constraint\NotTooManyTasksConstraint;
use App\Entity\Planning;
use App\Service\Planner\BacktrackingPlanner;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PlanningCrudController extends AbstractCrudController
{
    public function __construct(
        private BacktrackingPlanner $planner,
        ValidatorInterface $validator
    )
    {
        $this->planner->addConstraint(new AssignmentValidatorConstraint($validator));
        $this->planner->addConstraint(new NotTooManyTasksConstraint);
        $this->planner->addConstraint(new NoSpecialistConstraint);
    }

    public static function getEntityFqcn(): string
    {
        return Planning::class;
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('title', 'Title'),
            AssociationField::new('taskTypes', 'Task types'),
            AssociationField::new('persons', 'Persons'),
            IntegerField::new('gameCount', 'Number of games'),
        ];
    }
    
    public function configureActions(Actions $actions): Actions
    {
        $actions->add(
            Crud::PAGE_EDIT,
            Action::new('makeAssignment', 'Compute a planning', 'fa fa-calendar-alt')
                ->linkToCrudAction('makeAssignment')
        );

        $actions->add(
            Crud::PAGE_INDEX,
            Action::new('makeAssignment', 'Compute a planning')
                ->linkToCrudAction('makeAssignment')
        );

        return $actions;
    }

    public function makeAssignment(AdminContext $context)
    {
        $planning = $context->getEntity()->getInstance();
        $assignment = $this->planner->makeAssignment($planning);

        return $this->render(
            'admin/assignment.html.twig',
            [
                'assignment' => $assignment->toArray(),
                'planning' => $planning
            ]
        );
    }
}
