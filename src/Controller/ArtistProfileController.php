<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ArtistProfile;
use App\Entity\User;
use App\Form\ArtistProfileType;
use App\Repository\ArtistProfileRepository;
use App\Exception\ImageUploadException;
use App\Service\ImageUploadPreset;
use App\Service\ImageUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ArtistProfileController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ImageUploader $imageUploader,
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
            try {
                $this->handleImageUploads($form, $profile);
            } catch (ImageUploadException $exception) {
                $this->addFlash('error', $exception->getMessage());

                return $this->render('artist_profile/edit.html.twig', [
                    'form' => $form,
                    'profile' => $profile,
                    'completion' => $this->calculateProfileCompletion($profile),
                    'profilePictureThumb' => $this->imageUploader->getThumbPath($profile->getProfilePicture()),
                    'coverPictureUrl' => $profile->getCoverPicture(),
                ]);
            }

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
            'profilePictureThumb' => $this->imageUploader->getThumbPath($profile->getProfilePicture()),
            'coverPictureUrl' => $profile->getCoverPicture(),
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

    private function handleImageUploads(FormInterface $form, ArtistProfile $profile): void
    {
        $profilePictureFile = $form->get('profilePictureFile')->getData();
        if ($profilePictureFile !== null) {
            $result = $this->imageUploader->upload(
                $profilePictureFile,
                ImageUploadPreset::ArtistProfile,
                $profile->getProfilePicture(),
            );
            $profile->setProfilePicture($result->getMainPath());
        }

        $coverPictureFile = $form->get('coverPictureFile')->getData();
        if ($coverPictureFile !== null) {
            $result = $this->imageUploader->upload(
                $coverPictureFile,
                ImageUploadPreset::ArtistCover,
                $profile->getCoverPicture(),
            );
            $profile->setCoverPicture($result->getMainPath());
        }
    }
}
