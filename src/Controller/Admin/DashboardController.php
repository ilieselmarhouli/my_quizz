<?php

namespace App\Controller\Admin;

use App\Entity\Categorie;
use App\Entity\Question;
use App\Entity\Reponse;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(private AdminUrlGenerator $adminUrlGenerator)
    {
        
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // return parent::index();
        $url = $this->adminUrlGenerator
            ->setController(UserCrudController::class)
            ->generateUrl();
        return $this->redirect($url);
        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('My Quizz');
    }

    public function configureMenuItems(): iterable
    {

        yield MenuItem::section('User');
        yield MenuItem::subMenu('Actions', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud("Create a user", 'fas fa-plus', User::Class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud("Show users", 'fas fa-plus', User::Class)
        ]);

        yield MenuItem::section('Categorie');
        yield MenuItem::subMenu('Actions', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud("Create a categorie", 'fas fa-plus', Categorie::Class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud("Show categories", 'fas fa-plus', Categorie::Class)
        ]);

        yield MenuItem::section('Questions');
        yield MenuItem::subMenu('Actions', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud("Create a question", 'fas fa-plus', Question::Class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud("Show questions", 'fas fa-plus', Question::Class)
        ]);

        yield MenuItem::section('Reponses');
        yield MenuItem::subMenu('Actions', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud("Create a reponse", 'fas fa-plus', Reponse::Class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud("Show reponses", 'fas fa-plus', Reponse::Class)
        ]);

    
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
