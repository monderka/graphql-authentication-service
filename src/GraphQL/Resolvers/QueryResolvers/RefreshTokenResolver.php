<?php

namespace App\GraphQL\Resolvers\QueryResolvers;

use App\Exceptions\InvalidRefreshTokenException;
use App\Interfaces\GraphQLResolverInterface;
use App\Models\Entities\RefreshTokenEntity;
use App\Services\RefreshTokensService;
use JsonException;
use Monderka\DoctrineTools\Exceptions\EntityNotFoundException;
use Monderka\JwtGenerator\WebTokenGenerator;
use Nette\Security\Passwords;

final class RefreshTokenResolver implements GraphQLResolverInterface
{
    private string $issuer = "test";

    public function __construct(
        private readonly RefreshTokensService $refreshTokensService,
        private readonly WebTokenGenerator $webTokenGenerator,
        private readonly Passwords $passwords
    ) {
    }

    public function setIssuer(string $issuer): void
    {
        $this->issuer = $issuer;
    }

    /**
     * @param array{ "refreshToken": string } $parameters
     * @param array<string, mixed> $options
     * @return array{ "accessToken": string, "refreshToken": string }
     * @throws InvalidRefreshTokenException
     * @throws EntityNotFoundException
     * @throws JsonException
     */
    public function resolve(array $parameters, array $options = []): array
    {
        // parse token data
        list($id, $secret) = RefreshTokenEntity::parseString($parameters["refreshToken"]);

        // load refresh token
        $refreshToken = $this->refreshTokensService->get($id);

        // validate secret
        if (!$refreshToken->validatePassword($this->passwords, $secret)) {
            throw new InvalidRefreshTokenException();
        }

        // increase counter
        $refreshToken->increaseCounter();
        $this->refreshTokensService->save($refreshToken);

        // create access token
        $user = $refreshToken->getUser();
        $accessToken = $this->webTokenGenerator->generate(
            $this->issuer,
            $user->getId(),
            $user->getEmail()
        );

        // format result
        return [
            "accessToken" => $accessToken,
            "refreshToken" => $parameters["refreshToken"]
        ];
    }
}
