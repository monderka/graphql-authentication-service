<?php

namespace App\GraphQL\Mutations;

use App\Exceptions\EmailNotUniqueException;
use App\GraphQL\Inputs\RegisterInput;
use App\GraphQL\Resolvers\MutationResolvers\RegisterResolver;
use App\GraphQL\Types\CurrentUserType;
use GraphQL\Type\Definition\Type;
use Portiny\GraphQL\Contract\Mutation\MutationFieldInterface;

final class RegisterMutation implements MutationFieldInterface
{
    public function __construct(
        private readonly CurrentUserType $currentUserType,
        private readonly RegisterInput $registerInput,
        private readonly RegisterResolver $registerResolver
    ) {
    }

    public function getName(): string
    {
        return "register";
    }

    public function getDescription(): string
    {
        return "Register new user";
    }

    public function getType(): Type
    {
        return $this->currentUserType;
    }

    /**  @return array<string, array<string, mixed>> */
    public function getArgs(): array
    {
        return [
            "data" => Type::nonNull($this->registerInput)
        ];
    }

    /**
     * @param array<int|string, mixed> $root
     * @param array{ "data": array{ "email": string } }$args
     * @param mixed $context
     * @return array<string, mixed>
     * @throws EmailNotUniqueException
     */
    public function resolve(array $root, array $args, $context = null): array
    {
        return $this->registerResolver->resolve($args);
    }
}
