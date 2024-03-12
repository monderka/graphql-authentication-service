<?php

namespace App\GraphQL\Mutations;

use App\Exceptions\UserNotFoundException;
use App\GraphQL\Inputs\StartResetPasswordInput;
use App\GraphQL\Resolvers\MutationResolvers\StartResetPasswordResolver;
use App\GraphQL\Types\CurrentUserType;
use GraphQL\Type\Definition\Type;
use Portiny\GraphQL\Contract\Mutation\MutationFieldInterface;

final class StartResetPasswordMutation implements MutationFieldInterface
{
    public function __construct(
        private readonly CurrentUserType $currentUserType,
        private readonly StartResetPasswordInput $startResetPasswordInput,
        private readonly StartResetPasswordResolver $startResetPasswordResolver
    ) {
    }

    public function getName(): string
    {
        return "startResetPassword";
    }

    public function getDescription(): string
    {
        return "Start reset user's password";
    }

    public function getType(): Type
    {
        return $this->currentUserType;
    }

    /**  @return array<string, array<string, mixed>> */
    public function getArgs(): array
    {
        return [
            "data" => Type::nonNull($this->startResetPasswordInput)
        ];
    }

    /**
     * @param array<int|string, mixed> $root
     * @param array{ "data": array{ "email": string } }$args
     * @param mixed $context
     * @return array<string, mixed>
     * @throws UserNotFoundException
     */
    public function resolve(array $root, array $args, $context = null): array
    {
        return $this->startResetPasswordResolver->resolve($args);
    }
}
