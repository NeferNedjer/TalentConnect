<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ArtistProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ArtistController extends AbstractController
{
    private const PER_PAGE = 20;

    #[Route('/artists', name: 'app_artists')]
    public function index(Request $request, ArtistProfileRepository $artistProfileRepository): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        $totalItems = $artistProfileRepository->countPublicProfiles();
        $totalPages = max(1, (int) ceil($totalItems / self::PER_PAGE));

        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $artists = $artistProfileRepository->findPublicProfilesPaginated(
            ($page - 1) * self::PER_PAGE,
            self::PER_PAGE,
        );

        return $this->render('artists/index.html.twig', [
            'artists' => $artists,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalItems' => $totalItems,
            'perPage' => self::PER_PAGE,
        ]);
    }
}
