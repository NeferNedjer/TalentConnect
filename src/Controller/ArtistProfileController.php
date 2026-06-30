<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ArtistProfile;
use App\Entity\User;
use App\Form\ArtistProfileType;
use App\Repository\ArtistProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ArtistProfileController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/artist/profile/edit', name: 'app_artist_profile_edit')]
    #[IsGranted('ROLE_ARTIST')]
    public function edit(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $profile = $user->getArtistProfile();

        if ($profile === null) {
            return $this->redirectToRoute('app_choose_profile');
        }

        $form = $this->createForm(ArtistProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profile->setProfileCompletion($this->calculateProfileCompletion($profile));
            $profile->setUpdatedAt(new \DateTimeImmutable());

            $this->entityManager->flush();

            $this->addFlash('success', 'Votre profil a été mis à jour.');

            return $this->redirectToRoute('app_artist_profile_edit');
        }

        return $this->render('artist_profile/edit.html.twig', [
            'form' => $form,
            'profile' => $profile,
            'completion' => $this->calculateProfileCompletion($profile),
        ]);
    }

    #[Route('/artist/{slug}', name: 'app_artist_profile_show', requirements: ['slug' => '[a-zA-Z0-9\-_]+'])]
    public function show(string $slug, ArtistProfileRepository $artistProfileRepository): Response
    {
        $profile = $artistProfileRepository->findOneBySlug($slug);

        if ($profile === null) {
            throw $this->createNotFoundException('Profil artiste introuvable.');
        }

        return $this->render('artist_profile/show.html.twig', [
            'profile' => $profile,
        ]);
    }

    private function calculateProfileCompletion(ArtistProfile $profile): int
    {
        $fields = [
            $profile->getStageName(),
            $profile->getBio(),
            $profile->getWebsite(),
            $profile->getCity(),
            $profile->getPostalCode(),
            $profile->getRegion(),
            $profile->getCountry(),
            $profile->getArtistType(),
            $profile->getSpotifyUrl(),
            $profile->getYoutubeUrl(),
            $profile->getInstagramUrl(),
            $profile->getFacebookUrl(),
            $profile->getTiktokUrl(),
        ];

        $filled = 0;

        foreach ($fields as $value) {
            if ($value !== null && trim((string) $value) !== '') {
                ++$filled;
            }
        }

        return (int) round($filled / \count($fields) * 100);
    }
}
