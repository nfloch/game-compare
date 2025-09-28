<?php

namespace App\Controller;

use App\Security\SteamUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    #[Route('/index', name: 'app_index')]
    public function index(SteamUser $user): Response
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'user' => $user,
            'serializedUser' => json_encode($user)
        ]);
    }
}
