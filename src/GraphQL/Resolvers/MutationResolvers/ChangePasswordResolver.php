<?php

namespace App\GraphQL\Resolvers\MutationResolvers;

use App\Exceptions\InvalidPasswordException;
use App\Interfaces\GraphQLResolverInterface;
use App\Services\IdentityProvider;
use App\Services\UsersService;
use Monderka\DoctrineTools\Exceptions\EntityNotFoundException;
use Monderka\JwtParser\InvalidWebTokenException;
use Nette\Security\Passwords;

final class ChangePasswordResolver implements GraphQLResolverInterface
{
    public function __construct(
        private readonly IdentityProvider $identityProvider,
        private readonly UsersService $usersService,
        private readonly Passwords $passwords
    ) {
    }

    /**
     * @param array{
     *     "data": array{
     *         "oldPassword": string,
     *         "newPassword": string,
     *         "verifyPassword": string
     *     }
     * } $parameters
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     * @throws InvalidWebTokenException
     * @throws InvalidPasswordException
     * @throws EntityNotFoundException
     */
    public function resolve(array $parameters, array $options = []): array
    {
        // auth user
        $identity = $this->identityProvider->getIdentity();
        $data = $parameters["data"];

        // load user entity
        $user = $this->usersService->get($identity->id);

        // check new password
        if ($data["newPassword"] !== $data["verifyPassword"]) {
            throw new InvalidPasswordException("New password and verify password must be same");
        }

        // check old password
        if ($user->validatePassword($this->passwords, $data["oldPassword"])) {
            $user->increaseCounter();
            $this->usersService->save($user);
            throw new InvalidPasswordException("Invalid old password");
        }

        // save new password
        $user->setPassword($this->passwords, $data["newPassword"]);

        // format results
        return $user->toGraphQLOutput();
    }
}
