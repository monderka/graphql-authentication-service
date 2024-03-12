<?php

namespace App\GraphQL\Mutations;

use App\Exceptions\InvalidPasswordException;
use App\Exceptions\InvalidResetTokenException;
use App\GraphQL\Inputs\FinishResetPasswordInput;
use App\GraphQL\Resolvers\MutationResolvers\FinishResetPasswordResolver;
use App\GraphQL\Types\CurrentUserType;
use GraphQL\Type\Definition\Type;
use Monderka\DoctrineTools\Exceptions\EntityNotFoundException;
use Portiny\GraphQL\Contract\Mutation\MutationFieldInterface;

final class FinishResetPasswordMutation implements MutationFieldInterface
{
    public function __construct(
        private readonly CurrentUserType $currentUserType,
        private readonly FinishResetPasswordInput $finishResetPasswordInput,
        private readonly FinishResetPasswordResolver $finishResetPasswordResolver
    ) {
    }
    public function getName(): string
    {
        return "finishResetPassword";
    }

    public function getDescription(): string
    {
        return "Finish reset user's password";
    }

    public function getType(): Type
    {
        return $this->currentUserType;
    }

    /**  @return array<string, array<string, mixed>> */
    public function getArgs(): array
    {
        return [
            "data" => Type::nonNull($this->finishResetPasswordInput)
        ];
    }

    /**
     * @param array<int|string, mixed> $root
     * @param array{
     *     "data": array{
     *         "resetToken": string,
     *         "newPassword": string,
     *         "verifyPassword": string
     *     }
     * } $args
     * @param mixed $context
     * @return array<string, mixed>
     * @throws InvalidResetTokenException
     * @throws EntityNotFoundException
     * @throws InvalidPasswordException
     */
    public function resolve(array $root, array $args, $context = null): array
    {
        return $this->finishResetPasswordResolver->resolve($args);
    }
}
