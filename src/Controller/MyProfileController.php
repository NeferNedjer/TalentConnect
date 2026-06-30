<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class MyProfileController extends AbstractController
{
    // TODO: rediriger ROLE_ARTIST vers le profil artiste et ROLE_PRO vers le profil professionnel.
    #[Route('/my-profile', name: 'app_my_profile')]
    public function index(): Response
    {
        return $this->render('profile/my_profile.html.twig');
    }
}
