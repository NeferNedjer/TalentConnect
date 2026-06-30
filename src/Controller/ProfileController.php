<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ArtistProfile;
use App\Entity\ProfessionalProfile;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    #[Route('/profile/create/artist', name: 'app_artist_profile_create')]
    public function createArtist(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($user->getArtistProfile() !== null) {
            return $this->redirectToRoute('app_home');
        }

        $profile = new ArtistProfile();
        $profile->setUser($user);
        $profile->setStageName('');
        $profile->setSlug(uniqid('artist-'));
        $profile->setArtistType('solo');

        $user->setArtistProfile($profile);
        $this->addRole($user, 'ROLE_ARTIST');

        $this->entityManager->persist($profile);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        if ($this->routeExists('app_artist_profile_edit')) {
            return $this->redirectToRoute('app_artist_profile_edit');
        }

        // TODO: remplacer par app_artist_profile_edit lorsque la route d'édition sera disponible
        return $this->redirectToRoute('app_home');
    }

    #[Route('/profile/create/professional', name: 'app_professional_profile_create')]
    public function createProfessional(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($user->getProfessionalProfile() !== null) {
            return $this->redirectToRoute('app_home');
        }

        $profile = new ProfessionalProfile();
        $profile->setUser($user);
        $profile->setCompanyName('');
        $profile->setSlug(uniqid('pro-'));
        $profile->setType('other');

        $user->setProfessionalProfile($profile);
        $this->addRole($user, 'ROLE_PRO');

        $this->entityManager->persist($profile);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        if ($this->routeExists('app_professional_profile_edit')) {
            return $this->redirectToRoute('app_professional_profile_edit');
        }

        return $this->redirectToRoute('app_home');
    }

    private function addRole(User $user, string $role): void
    {
        $roles = $user->getRoles();

        if (!\in_array($role, $roles, true)) {
            $roles[] = $role;
        }

        $user->setRoles($roles);
    }

    private function routeExists(string $name): bool
    {
        try {
            $this->urlGenerator->generate($name);

            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}
