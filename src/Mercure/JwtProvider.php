<?php

namespace App\Mercure;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Plain;

final class JwtProvider
{
    /**
     * @param string $secret
     */
    public function __construct(
        private string $secret
    )
    {
    }

    /**
     * @return string
     */
    public function __invoke(): string
    {
        $config = Configuration::forSymmetricSigner(new Sha256(), InMemory::plainText($this->secret));
        return $config->builder()
            ->withClaim('mercure', ['publish' => ['*']])
            ->getToken($config->signer(), $config->signingKey())
            ->toString();
    }
}
