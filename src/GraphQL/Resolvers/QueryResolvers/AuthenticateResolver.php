<?php

namespace App\GraphQL\Resolvers\QueryResolvers;

use App\Exceptions\InvalidPasswordException;
use App\Exceptions\MaxAttemptsReachedException;
use App\Exceptions\UserNotActiveException;
use App\Exceptions\UserNotFoundException;
use App\Interfaces\GraphQLResolverInterface;
use App\Models\Entities\RefreshTokenEntity;
use App\Models\Entities\UserEntity;
use App\Services\RefreshTokensService;
use App\Services\UsersService;
use JsonException;
use Monderka\JwtGenerator\WebTokenGenerator;
use Nette\Security\Passwords;

final class AuthenticateResolver implements GraphQLResolverInterface
{
    private string $issuer = "test";

    public const MAX_ATTEMPTS = 5;

    public function __construct(
        private readonly UsersService $usersService,
        private readonly RefreshTokensService $refreshTokensService,
        private readonly Passwords $passwords,
        private readonly WebTokenGenerator $webTokenGenerator
    ) {
    }

    public function setJwtIssuer(string $issuer): void
    {
        $this->issuer = $issuer;
    }

    /**
     * @param array{ "data": array{ "email": string, "password": string }} $parameters
     * @param array<string, mixed> $options
     * @return array{ "accessToken": string, "refreshToken": string }
     * @throws UserNotFoundException
     * @throws UserNotActiveException
     * @throws MaxAttemptsReachedException
     * @throws InvalidPasswordException
     * @throws JsonException
     */
    public function resolve(array $parameters, array $options = []): array
    {
        // load user
        $data = $parameters["data"];
        $user = $this->usersService->findByEmail($data["email"]);

        // test active
        if (!$user->isActive()) {
            throw new UserNotActiveException();
        }

        // test counter
        if ($user->getCounter() >= self::MAX_ATTEMPTS) {
            throw new MaxAttemptsReachedException();
        }

        // test password
        if (!$user->validatePassword($this->passwords, $data["password"])) {
            $user->increaseCounter();
            $this->usersService->save($user);
            throw new InvalidPasswordException();
        }

        // clear counter
        $user->clearCounter();
        $this->usersService->save($user);

        // create refresh token
        $refreshToken = (string) $this->createRefreshToken($user);

        // create access token
        $accessToken = $this->webTokenGenerator->generate(
            $this->issuer,
            $user->getId(),
            $user->getEmail()
        );

        // format result
        return [
            "accessToken" => $accessToken,
            "refreshToken" => $refreshToken
        ];
    }

    private function createRefreshToken(UserEntity $user): RefreshTokenEntity
    {
        $this->refreshTokensService->clearExpiredTokens();
        $refreshToken = (new RefreshTokenEntity())
            ->setUser($user);
        $refreshToken->generatePassword($this->passwords);
        $this->refreshTokensService->save($refreshToken);
        return $refreshToken;
    }
}
