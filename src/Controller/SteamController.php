<?php

namespace App\Controller;

use App\Dto\SteamUserDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/steam')]
final class SteamController extends AbstractController
{
    public function __construct(
        private HttpClientInterface $client,
    )
    {
    }

    #[Route('/callback', 'steam.callback')]
    public function callback(
        #[MapQueryString] SteamUserDto $steamUserDto,
    ): JsonResponse
    {
        $response = $this->client->request(
            'POST',
            $steamUserDto->openid_op_endpoint,
            [
                'body' => $steamUserDto->toValidateParams(),
            ]
        );

        if (false === str_contains($response->getContent(), 'is_valid:true')) {
            throw new BadRequestException('user is not authenticated');
        }

        return $this->json([
            'steamUser' => $steamUserDto,
        ]);
    }

    #[Route('/login')]
    public function login(): Response
    {
        return $this->render('steam/login.html.twig');
    }
}
