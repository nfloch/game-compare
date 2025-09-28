<?php

namespace App\Controller;

use App\Dto\SteamUserDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/steam')]
final class SteamController extends AbstractController
{
    #[Route('/login', 'steam.login')]
    public function login(): Response
    {
        return $this->render('steam/login.html.twig');
    }
}
