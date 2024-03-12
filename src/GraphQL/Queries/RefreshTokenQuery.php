<?php

namespace App\GraphQL\Queries;

use App\Exceptions\InvalidRefreshTokenException;
use App\GraphQL\Resolvers\QueryResolvers\RefreshTokenResolver;
use App\GraphQL\Types\AccessTokenType;
use GraphQL\Type\Definition\Type;
use JsonException;
use Monderka\DoctrineTools\Exceptions\EntityNotFoundException;
use Portiny\GraphQL\Contract\Field\QueryFieldInterface;

final class RefreshTokenQuery implements QueryFieldInterface
{
    public function __construct(
        private readonly AccessTokenType $accessTokenType,
        private readonly RefreshTokenResolver $refreshTokenResolver
    ) {
    }

    public function getName(): string
    {
        return "refreshToken";
    }

    public function getDescription(): string
    {
        return "Refresh access token";
    }

    public function getType(): Type
    {
        return $this->accessTokenType;
    }

    /**  @return array<string, array<string, mixed>> */
    public function getArgs(): array
    {
        return [
            "refreshToken" => Type::nonNull(Type::string())
        ];
    }

    /**
     * @param array<int|string, mixed> $root
     * @param array{ "refreshToken": string } $args
     * @param mixed $context
     * @return array{ "accessToken": string, "refreshToken": string }
     * @throws InvalidRefreshTokenException
     * @throws JsonException
     * @throws EntityNotFoundException
     */
    public function resolve(array $root, array $args, $context = null): array
    {
        return $this->refreshTokenResolver->resolve($args);
    }
}
