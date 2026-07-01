<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\ArtistProfile;
use App\Entity\ProfessionalProfile;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function index(): Response
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $artistRepository = $this->entityManager->getRepository(ArtistProfile::class);
        $professionalRepository = $this->entityManager->getRepository(ProfessionalProfile::class);

        /** @var AdminUrlGenerator $adminUrlGenerator */
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        return $this->render('admin/dashboard.html.twig', [
            'stats' => [
                'users' => $userRepository->count([]),
                'artists' => $artistRepository->count(['deletedAt' => null]),
                'professionals' => $professionalRepository->count(['deletedAt' => null]),
                'videos' => 0,
                'opportunities' => 0,
            ],
            'latestUsers' => $userRepository->findBy([], ['createdAt' => 'DESC'], 5),
            'urls' => [
                'users' => $adminUrlGenerator->setController(UserCrudController::class)->generateUrl(),
                'artists' => $adminUrlGenerator->setController(ArtistProfileCrudController::class)->generateUrl(),
                'professionals' => $adminUrlGenerator->setController(ProfessionalProfileCrudController::class)->generateUrl(),
                'home' => $this->generateUrl('app_home'),
            ],
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('TalentConnect — Administration');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkTo(UserCrudController::class, 'Utilisateurs', 'fa fa-users');
        yield MenuItem::linkTo(ArtistProfileCrudController::class, 'Profils artistes', 'fa fa-music');
        yield MenuItem::linkTo(GenreCrudController::class, 'Genres', 'fa-solid fa-music');
        yield MenuItem::linkTo(ProfessionalProfileCrudController::class, 'Profils professionnels', 'fa fa-briefcase');
        yield MenuItem::linkToRoute('Retour au site', 'fa fa-arrow-left', 'app_home');
    }
}
