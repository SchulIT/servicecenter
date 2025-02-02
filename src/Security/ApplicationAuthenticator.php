<?php

namespace App\Security;

use App\Repository\ApplicationRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class ApplicationAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface {

    public const string HEADER_KEY = 'X-Token';

    public function __construct(private readonly ApplicationRepositoryInterface $repository)
    {
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request): bool {
        return $request->headers->has(static::HEADER_KEY);
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response {
        return new JsonResponse([
            'success' => false,
            'message' => sprintf('Authentication failed: %s', $exception->getMessage())
        ], Response::HTTP_FORBIDDEN);
    }

    /**
     * @inheritDoc
     */
    public function start(Request $request, AuthenticationException $authException = null): Response {
        return new JsonResponse([
            'success' => false,
            'message' => 'Authentication required'
        ], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $firewallName): ?Response {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function authenticate(Request $request): Passport {
        $token = $request->headers->get(static::HEADER_KEY);

        if($token === null) {
            throw new CustomUserMessageAuthenticationException('No API token provided.');
        }

        $user = $this->repository->findOneByApiKey($token);

        if($user === null) {
            throw new CustomUserMessageAuthenticationException('Invalid token.');
        }

        return new SelfValidatingPassport(new UserBadge($user->getApiKey()));
    }
}