<?php

namespace App\GraphQL\Types;

use App\GraphQL\Scalars\EmailScalar;
use App\Interfaces\GraphQLTypeInterface;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

final class CurrentUserType extends ObjectType implements GraphQLTypeInterface
{
    public function __construct(
        EmailScalar $emailScalar
    ) {
        parent::__construct([
            "name" => "CurrentUser",
            'fields' => [
                "email" => Type::nonNull($emailScalar),
                "active" => Type::nonNull(Type::boolean())
            ]
        ]);
    }
}
