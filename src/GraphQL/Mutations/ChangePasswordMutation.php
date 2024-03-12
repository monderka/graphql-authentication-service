<?php

namespace App\GraphQL\Mutations;

use App\Exceptions\InvalidPasswordException;
use App\GraphQL\Inputs\ChangePasswordInput;
use App\GraphQL\Resolvers\MutationResolvers\ChangePasswordResolver;
use App\GraphQL\Types\CurrentUserType;
use GraphQL\Type\Definition\Type;
use Monderka\DoctrineTools\Exceptions\EntityNotFoundException;
use Monderka\JwtParser\InvalidWebTokenException;
use Portiny\GraphQL\Contract\Mutation\MutationFieldInterface;

final class ChangePasswordMutation implements MutationFieldInterface
{
    public function __construct(
        private readonly CurrentUserType $currentUserType,
        private readonly ChangePasswordInput $changePasswordInput,
        private readonly ChangePasswordResolver $changePasswordResolver
    ) {
    }

    public function getName(): string
    {
        return "changePassword";
    }

    public function getDescription(): string
    {
        return "Change current user's password";
    }

    public function getType(): Type
    {
        return $this->currentUserType;
    }

    /**  @return array<string, array<string, mixed>> */
    public function getArgs(): array
    {
        return [
            "data" => Type::nonNull($this->changePasswordInput)
        ];
    }

    /**
     * @param array<int|string, mixed> $root
     * @param array{
     *     "data": array{
     *         "oldPassword": string,
     *         "newPassword": string,
     *         "verifyPassword": string
     *     }
     * } $args
     * @param mixed $context
     * @return array<string, mixed>
     * @throws InvalidPasswordException
     * @throws EntityNotFoundException
     * @throws InvalidWebTokenException
     */
    public function resolve(array $root, array $args, $context = null): array
    {
        return $this->changePasswordResolver->resolve($args);
    }
}
