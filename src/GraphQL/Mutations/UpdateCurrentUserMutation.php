<?php

namespace App\GraphQL\Mutations;

use App\Exceptions\EmailNotUniqueException;
use App\GraphQL\Inputs\UpdateCurrentUserInput;
use App\GraphQL\Resolvers\MutationResolvers\UpdateCurrentUserResolver;
use App\GraphQL\Types\CurrentUserType;
use GraphQL\Type\Definition\Type;
use Monderka\DoctrineTools\Exceptions\EntityNotFoundException;
use Monderka\JwtParser\InvalidWebTokenException;
use Portiny\GraphQL\Contract\Mutation\MutationFieldInterface;

final class UpdateCurrentUserMutation implements MutationFieldInterface
{
    public function __construct(
        private readonly CurrentUserType $currentUserType,
        private readonly UpdateCurrentUserInput $updateCurrentUserInput,
        private readonly UpdateCurrentUserResolver $updateCurrentUserResolver
    ) {
    }

    public function getName(): string
    {
        return "updateCurrentUser";
    }

    public function getDescription(): string
    {
        return "Update current user's data";
    }

    public function getType(): Type
    {
        return $this->currentUserType;
    }

    /**  @return array<string, array<string, mixed>> */
    public function getArgs(): array
    {
        return [
            "data" => Type::nonNull($this->updateCurrentUserInput)
        ];
    }

    /**
     * @param array<int|string, mixed> $root
     * @param array{ "data": array{ "email"?: string, "active"?: boolean }} $args
     * @param mixed $context
     * @return array<string, mixed>
     * @throws EntityNotFoundException
     * @throws InvalidWebTokenException
     * @throws EmailNotUniqueException
     */
    public function resolve(array $root, array $args, $context = null): array
    {
        return $this->updateCurrentUserResolver->resolve($args);
    }
}
