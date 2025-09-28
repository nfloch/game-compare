<?php

namespace App\Dto;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class SteamUserDto
{
    #[Assert\NotBlank]
    #[Assert\EqualTo('http://specs.openid.net/auth/2.0')]
    public string $openid_ns;

    #[Assert\NotBlank]
    public string $openid_mode;

    #[Assert\NotBlank]
    #[Assert\EqualTo('https://steamcommunity.com/openid/login')]
    public string $openid_op_endpoint;

    #[Assert\NotBlank]
    #[Assert\Expression('this.openid_claimed_id === this.openid_identity')]
    public string $openid_claimed_id;

    #[Assert\NotBlank]
    public string $openid_identity;

    #[Assert\NotBlank]
//    #[SteamAssert\MatchesLoginCallbackRoute]
    public string $openid_return_to;

    #[Assert\NotBlank]
    public string $openid_response_nonce;

    #[Assert\NotBlank]
    public string $openid_assoc_handle;

    #[Assert\NotBlank]
    public string $openid_signed;

    #[Assert\NotBlank]
    public string $openid_sig;

    public function getCommunityId(): string
    {
        return str_replace('https://steamcommunity.com/openid/id/', '', $this->openid_identity);
    }

    public function toValidateParams(): array
    {
        return [
            'openid.ns' => $this->openid_ns,
            'openid.mode' => 'check_authentication',
            'openid.op_endpoint' => $this->openid_op_endpoint,
            'openid.claimed_id' => $this->openid_claimed_id,
            'openid.identity' => $this->openid_identity,
            'openid.return_to' => $this->openid_return_to,
            'openid.response_nonce' => $this->openid_response_nonce,
            'openid.assoc_handle' => $this->openid_assoc_handle,
            'openid.signed' => $this->openid_signed,
            'openid.sig' => $this->openid_sig,
            ];
    }
}
