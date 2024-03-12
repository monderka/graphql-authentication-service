<?php

namespace App\GraphQL\Queries;

use App\Exceptions\InvalidPasswordException;
use App\Exceptions\MaxAttemptsReachedException;
use App\Exceptions\UserNotActiveException;
use App\Exceptions\UserNotFoundException;
use App\GraphQL\Inputs\AuthenticateInput;
use App\GraphQL\Resolvers\QueryResolvers\AuthenticateResolver;
use App\GraphQL\Types\AccessTokenType;
use GraphQL\Type\Definition\Type;
use Portiny\GraphQL\Contract\Field\QueryFieldInterface;
use JsonException;

final class AuthenticateQuery implements QueryFieldInterface
{
    public function __construct(
        private readonly AccessTokenType $accessTokenType,
        private readonly AuthenticateInput $authenticateInput,
        private readonly AuthenticateResolver $authenticateResolver
    ) {
    }

    public function getName(): string
    {
        return "authenticate";
    }

    public function getDescription(): string
    {
        return "Authenticate user via email and password";
    }

    public function getType(): Type
    {
        return $this->accessTokenType;
    }

    /**  @return array<string, array<string, mixed>> */
    public function getArgs(): array
    {
        return [
            "data" => Type::nonNull($this->authenticateInput)
        ];
    }

    /**
     * @param array<int|string, mixed> $root
     * @param array{ "data": array{ "email": string, "password": string }} $args
     * @param mixed $context
     * @return array{ "accessToken": string, "refreshToken": string }
     * @throws UserNotFoundException
     * @throws UserNotActiveException
     * @throws MaxAttemptsReachedException
     * @throws InvalidPasswordException
     * @throws JsonException
     */
    public function resolve(array $root, array $args, $context = null): array
    {
        return $this->authenticateResolver->resolve($args);
    }
}
