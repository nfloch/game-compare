<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class SteamWebApiClient
{
    public function __construct(
        #[Autowire(env: 'string:STEAM_WEB_API_KEY')]
        private string $steamApiKey,
        private HttpClientInterface $steamClient,
    )
    {
    }

    public function getOwnedGames(string $steamId): array
    {
        $response = $this->steamClient->request(
            'GET',
            '/IPlayerService/GetOwnedGames/v1',
            ['query' => [
                'steamid' => $steamId,
                'key' => $this->steamApiKey,
                'include_appinfo' => true,
            ]]
        );

        return $response->toArray()['response'];
    }

    public function getFriends(string $steamId): array
    {
        $response = $this->steamClient->request(
            'GET',
            '/ISteamUser/GetFriendList/v1',
            ['query' => [
                'steamid' => $steamId,
                'key' => $this->steamApiKey,
                'include_appinfo' => true,
            ]]
        );
    }
}
