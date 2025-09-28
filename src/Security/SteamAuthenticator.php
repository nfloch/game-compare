<?php

namespace App\Security;

use App\Dto\SteamUserDto;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @see https://symfony.com/doc/current/security/custom_authenticator.html
 */
final class SteamAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly HttpClientInterface $client,
        private readonly ValidatorInterface $validator,
    )
    {
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request): ?bool
    {
        return $request->get('openid_ns') !== null;
    }

    public function authenticate(Request $request): Passport
    {
        $steamUserDto = SteamUserDto::fromRequest($request);

        $violations = $this->validator->validate($steamUserDto);
        if (count($violations) > 0) {
            throw new CustomUserMessageAuthenticationException('Response is not a valid oauth steam user.');
        }

        $response = $this->client->request(
            'POST',
            $steamUserDto->openid_op_endpoint,
            [
                'body' => $steamUserDto->toValidateParams(),
            ]
        );

        if (false === str_contains($response->getContent(), 'is_valid:true')) {
            throw new CustomUserMessageAuthenticationException('Check authentication is not valid');
        }

        return new SelfValidatingPassport(
            new UserBadge($steamUserDto->getCommunityId()),
            [
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse(
            $this->router->generate('app_index')
        );
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return null;
    }

    public function start(Request $request, ?AuthenticationException $authException = null): Response
    {
        return new RedirectResponse(
            $this->router->generate('steam.login')
        );
    }
}
