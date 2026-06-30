<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class ProfileChoiceController extends AbstractController
{
    #[Route('/choose-profile', name: 'app_choose_profile')]
    public function choose(): Response
    {
        return $this->render('profile/choose.html.twig');
    }
}
