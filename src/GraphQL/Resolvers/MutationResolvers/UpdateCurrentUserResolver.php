<?php

namespace App\GraphQL\Resolvers\MutationResolvers;

use App\Exceptions\EmailNotUniqueException;
use App\Interfaces\GraphQLResolverInterface;
use App\Services\IdentityProvider;
use App\Services\UsersService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Monderka\DoctrineTools\Exceptions\EntityNotFoundException;
use Monderka\JwtParser\InvalidWebTokenException;

final class UpdateCurrentUserResolver implements GraphQLResolverInterface
{
    public function __construct(
        private readonly IdentityProvider $identityProvider,
        private readonly UsersService $usersService
    ) {
    }

    /**
     * @param array{
     *     "data": array{
     *         "email"?: string,
     *         "active"?: boolean
     *     }
     * } $parameters
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     * @throws EntityNotFoundException
     * @throws InvalidWebTokenException
     * @throws EmailNotUniqueException
     */
    public function resolve(array $parameters, array $options = []): array
    {
        try {
            // auth user
            $identity = $this->identityProvider->getIdentity();

            // load entity
            $user = $this->usersService->get($identity->id);

            // update and save
            $user->updateFromGraphQLInput($parameters["data"]);
            $this->usersService->save($user);

            // format result
            return $user->toGraphQLOutput();
        } catch (UniqueConstraintViolationException) {
            throw new EmailNotUniqueException();
        }
    }
}
