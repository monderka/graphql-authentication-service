<?php

namespace App\GraphQL\Inputs;

use App\GraphQL\Scalars\EmailScalar;
use App\Interfaces\GraphQLInputInterface;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

final class RegisterInput extends InputObjectType implements GraphQLInputInterface
{
    public function __construct(
        EmailScalar $emailScalar
    ) {
        parent::__construct([
            "name" => "RegisterInput",
            "fields" => [
                "email" => [
                    "type" => Type::nonNull($emailScalar)
                ]
            ]
        ]);
    }
}
