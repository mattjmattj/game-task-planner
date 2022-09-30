<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Person;
use App\Entity\Planning;
use App\Entity\TaskType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private AdminUrlGenerator $routeBuilder
    )
    {

    }

    #[Route('/')]
    public function index(): Response
    {
        return $this->redirect(
            $this->routeBuilder->setController(PlanningCrudController::class)->generateUrl()
        );
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Game task planner')
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Plannings', 'fas fa-calendar', Planning::class);
        yield MenuItem::section();
        yield MenuItem::linkToCrud('Persons', 'fas fa-user', Person::class);
        yield MenuItem::linkToCrud('Task types', 'fas fa-list', TaskType::class);
    }
}
