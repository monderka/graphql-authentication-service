<?php

namespace App\GraphQL\Resolvers\MutationResolvers;

use App\Exceptions\InvalidPasswordException;
use App\Exceptions\InvalidResetTokenException;
use App\Interfaces\GraphQLResolverInterface;
use App\Models\Entities\ResetTokenEntity;
use App\Services\ResetTokensService;
use App\Services\UsersService;
use Monderka\DoctrineTools\Exceptions\EntityNotFoundException;
use Nette\Security\Passwords;

final class FinishResetPasswordResolver implements GraphQLResolverInterface
{
    public function __construct(
        private readonly ResetTokensService $resetTokensService,
        private readonly UsersService $usersService,
        private readonly Passwords $passwords
    ) {
    }

    /**
     * @param array{
     *     "data": array{
     *         "resetToken": string,
     *         "newPassword": string,
     *         "verifyPassword": string
     *     }
     * } $parameters
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     * @throws InvalidResetTokenException
     * @throws EntityNotFoundException
     * @throws InvalidPasswordException
     */
    public function resolve(array $parameters, array $options = []): array
    {
        $this->resetTokensService->clearExpiredTokens();

        // parse token id
        $data = $parameters["data"];
        $parsedToken = ResetTokenEntity::parseString($data["resetToken"]);

        // load reset token
        $resetToken = $this->resetTokensService->get((int) $parsedToken["id"]);
        $user = $resetToken->getUser();

        // verify secret
        if (!$resetToken->validatePassword($this->passwords, $parsedToken["secret"])) {
            throw new InvalidResetTokenException();
        }

        // verify passwords
        if ($data["newPassword"] !== $data["verifyPassword"]) {
            throw new InvalidPasswordException("New password and verify password must be same");
        }

        // save new password
        $user->setPassword($this->passwords, $data["newPassword"]);

        // clear counter
        $user->clearCounter();
        $this->usersService->save($user);

        // format results
        return $user->toGraphQLOutput();
    }
}
