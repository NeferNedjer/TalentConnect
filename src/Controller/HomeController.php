<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\AnnouncementRepository;
use App\Repository\ArtistProfileRepository;
use App\Repository\ProfessionalProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        ArtistProfileRepository $artistProfileRepository,
        ProfessionalProfileRepository $professionalProfileRepository,
        AnnouncementRepository $announcementRepository,
    ): Response {
        return $this->render('home/index.html.twig', [
            'featuredArtists' => $artistProfileRepository->findLatestPublicProfiles(6),
            'latestAnnouncements' => $announcementRepository->findLatestPublished(4),
            'stats' => [
                'artists' => $artistProfileRepository->countPublicProfiles(),
                'professionals' => $professionalProfileRepository->countPublicProfiles(),
                'announcements' => $announcementRepository->countPublished(),
            ],
        ]);
    }
}
