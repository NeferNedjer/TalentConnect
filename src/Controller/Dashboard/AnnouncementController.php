<?php

declare(strict_types=1);

namespace App\Controller\Dashboard;

use App\Controller\DashboardController;
use App\Entity\Announcement;
use App\Entity\User;
use App\Form\AnnouncementType as AnnouncementFormType;
use App\Repository\AnnouncementRepository;
use App\Service\AnnouncementManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AnnouncementController extends DashboardController
{
    public function __construct(
        private readonly AnnouncementManager $announcementManager,
    ) {
    }

    #[Route('/dashboard/announcements', name: 'app_dashboard_announcements')]
    public function list(AnnouncementRepository $announcementRepository): Response
    {
        if ($response = $this->redirectUnlessHasProfile()) {
            return $response;
        }

        /** @var User $user */
        $user = $this->getUser();

        return $this->render('dashboard/announcements/index.html.twig', [
            'announcements' => $announcementRepository->findByCreatedBy($user),
        ]);
    }

    #[Route('/dashboard/announcements/create', name: 'app_dashboard_announcement_create')]
    public function create(Request $request): Response
    {
        if ($response = $this->redirectUnlessHasProfile()) {
            return $response;
        }

        /** @var User $user */
        $user = $this->getUser();
        $announcement = new Announcement();
        $this->announcementManager->initializeDraft($announcement, $user);

        $form = $this->createForm(AnnouncementFormType::class, $announcement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->announcementManager->publish($announcement, $user);

            $this->addFlash('success', 'Votre annonce a été publiée avec succès.');

            return $this->redirectToRoute('app_dashboard_announcements');
        }

        return $this->render('dashboard/announcements/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/dashboard/announcements/{id}', name: 'app_dashboard_announcement_show', requirements: ['id' => '\d+'])]
    public function showPlaceholder(int $id): Response
    {
        if ($response = $this->redirectUnlessHasProfile()) {
            return $response;
        }

        return $this->redirectToRoute('app_dashboard_announcements');
    }

    #[Route('/dashboard/announcements/{id}/edit', name: 'app_dashboard_announcement_edit', requirements: ['id' => '\d+'])]
    public function editPlaceholder(int $id): Response
    {
        if ($response = $this->redirectUnlessHasProfile()) {
            return $response;
        }

        return $this->redirectToRoute('app_dashboard_announcements');
    }

    #[Route('/dashboard/announcements/{id}/delete', name: 'app_dashboard_announcement_delete', requirements: ['id' => '\d+'])]
    public function deletePlaceholder(int $id): Response
    {
        if ($response = $this->redirectUnlessHasProfile()) {
            return $response;
        }

        return $this->redirectToRoute('app_dashboard_announcements');
    }
}
