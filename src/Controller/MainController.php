<?php

namespace App\Controller;

use App\Security\SteamUser;
use App\Service\SteamWebApiClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class MainController extends AbstractController
{
    public function __construct(
        private readonly SteamWebApiClient $steamClient,
    )
    {
    }
    #[Route('/', name: 'app_home')]
    public function home(): Response
    {
        return $this->render('main/home.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    #[Route('/index', name: 'app_index')]
    public function index(#[CurrentUser] SteamUser $user): Response
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'user' => $user,
            'serializedUser' => json_encode($user)
        ]);
    }

    #[Route('/games', name: 'app_games')]
    public function games(#[CurrentUser] SteamUser $user): JsonResponse
    {
        return $this->json($this->steamClient->getOwnedGames($user->getUsername()));
    }

    #[Route('/friends', name: 'app_friends')]
    public function friends(#[CurrentUser] SteamUser $user): JsonResponse
    {
        return $this->json($this->steamClient->getOwnedGames($user->getUsername()));
    }
}
