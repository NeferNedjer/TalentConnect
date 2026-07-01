<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ArtistProfileRepository;
use App\Repository\GenreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ArtistController extends AbstractController
{
    private const PER_PAGE = 20;

    #[Route('/artists', name: 'app_artists')]
    public function index(
        Request $request,
        ArtistProfileRepository $artistProfileRepository,
        GenreRepository $genreRepository,
    ): Response {
        $search = trim($request->query->getString('search', ''));
        $city = trim($request->query->getString('city', ''));
        $type = $request->query->getString('type', '');
        $genre = trim($request->query->getString('genre', ''));

        if ($type !== '' && !\in_array($type, ['solo', 'groupe'], true)) {
            $type = '';
        }

        $searchFilter = $search !== '' ? $search : null;
        $cityFilter = $city !== '' ? $city : null;
        $typeFilter = $type !== '' ? $type : null;
        $genreFilter = $genre !== '' ? $genre : null;

        if ($genreFilter !== null) {
            $genreExists = $genreRepository->findOneBySlug($genreFilter);
            if ($genreExists === null) {
                $genreFilter = null;
                $genre = '';
            }
        }

        $page = max(1, $request->query->getInt('page', 1));
        $totalItems = $artistProfileRepository->countPublicProfiles($searchFilter, $cityFilter, $typeFilter, $genreFilter);
        $totalPages = max(1, (int) ceil($totalItems / self::PER_PAGE));

        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $artists = $artistProfileRepository->findPublicProfilesPaginated(
            ($page - 1) * self::PER_PAGE,
            self::PER_PAGE,
            $searchFilter,
            $cityFilter,
            $typeFilter,
            $genreFilter,
        );

        return $this->render('artists/index.html.twig', [
            'artists' => $artists,
            'genres' => $genreRepository->findAllOrderedByName(),
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalItems' => $totalItems,
            'perPage' => self::PER_PAGE,
            'filters' => [
                'search' => $search,
                'city' => $city,
                'type' => $type,
                'genre' => $genre,
                'active' => $searchFilter !== null || $cityFilter !== null || $typeFilter !== null || $genreFilter !== null,
            ],
        ]);
    }
}
