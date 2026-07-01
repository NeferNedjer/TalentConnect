<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        return $this->render('dashboard/index.html.twig');
    }

    protected function redirectUnlessHasProfile(): ?Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        if ($user->getArtistProfile() === null && $user->getProfessionalProfile() === null) {
            $this->addFlash(
                'warning',
                'Pour accéder à cette section, vous devez d\'abord créer un profil artiste ou professionnel.',
            );

            return $this->redirectToRoute('app_dashboard');
        }

        return null;
    }

    protected function userHasProfile(): bool
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return false;
        }

        return $user->getArtistProfile() !== null || $user->getProfessionalProfile() !== null;
    }
}
