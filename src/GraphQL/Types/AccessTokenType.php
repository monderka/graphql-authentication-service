<?php

namespace App\GraphQL\Types;

use App\Interfaces\GraphQLTypeInterface;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

final class AccessTokenType extends ObjectType implements GraphQLTypeInterface
{
    public function __construct()
    {
        parent::__construct([
            "name" => "AccessToken",
            'fields' => [
                "accessToken" => Type::nonNull(Type::string()),
                "refreshToken" => Type::nonNull(Type::string())
            ]
        ]);
    }
}
