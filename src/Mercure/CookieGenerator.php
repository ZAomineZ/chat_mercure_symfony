<?php

namespace App\Mercure;

use App\Entity\User;
use DateInterval;
use DateTime;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class CookieGenerator
{
    /**
     * @param string $secret
     * @param TokenStorageInterface $tokenAuthentication
     */
    public function __construct(
        private string                $secret,
        private TokenStorageInterface $tokenAuthentication
    )
    {
    }

    /**
     * @return Cookie
     */
    public function generate(): Cookie
    {
        /** @var User $user */
        $user = $this->tokenAuthentication->getToken()->getUser();

        $config = Configuration::forSymmetricSigner(new Sha256(), InMemory::plainText($this->secret));
        $token = $config->builder()
            ->withClaim('mercure', ['subscribe' => [sprintf('/%s', $user->getUsername())]])
            ->getToken($config->signer(), $config->signingKey())
            ->toString();

        return new Cookie(
            'mercureAuthorization',
            $token,
            (new \DateTime())
                ->add(new \DateInterval('PT2H')),
            '/.well-known/mercure',
            null,
            false,
            true,
            false,
            'strict'
        );
    }
}
