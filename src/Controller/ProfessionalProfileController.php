<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ProfessionalProfile;
use App\Entity\User;
use App\Form\ProfessionalProfileType;
use App\Repository\ProfessionalProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProfessionalProfileController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/professional/profile/edit', name: 'app_professional_profile_edit')]
    #[IsGranted('ROLE_PRO')]
    public function edit(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $profile = $user->getProfessionalProfile();

        if ($profile === null) {
            return $this->redirectToRoute('app_choose_profile');
        }

        $form = $this->createForm(ProfessionalProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profile->setUpdatedAt(new \DateTimeImmutable());

            $this->entityManager->flush();

            $this->addFlash('success', 'Votre profil professionnel a été mis à jour.');

            return $this->redirectToRoute('app_professional_profile_edit');
        }

        return $this->render('professional_profile/edit.html.twig', [
            'form' => $form,
            'profile' => $profile,
            'completion' => $this->calculateProfileCompletion($profile),
        ]);
    }

    #[Route('/professional/{slug}', name: 'app_professional_profile_show', requirements: ['slug' => '[a-zA-Z0-9\-_]+'])]
    public function show(string $slug, ProfessionalProfileRepository $professionalProfileRepository): Response
    {
        $profile = $professionalProfileRepository->findOneBySlug($slug);

        if ($profile === null) {
            throw $this->createNotFoundException('Profil professionnel introuvable.');
        }

        return $this->render('professional_profile/show.html.twig', [
            'profile' => $profile,
        ]);
    }

    private function calculateProfileCompletion(ProfessionalProfile $profile): int
    {
        $fields = [
            $profile->getCompanyName(),
            $profile->getDescription(),
            $profile->getWebsite(),
            $profile->getEmailPublic(),
            $profile->getTelephonePublic(),
            $profile->getCity(),
            $profile->getPostalCode(),
            $profile->getRegion(),
            $profile->getCountry(),
            $profile->getType(),
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
