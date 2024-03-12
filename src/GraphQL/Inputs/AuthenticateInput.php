<?php

namespace App\GraphQL\Inputs;

use App\GraphQL\Scalars\EmailScalar;
use App\GraphQL\Scalars\PasswordScalar;
use App\Interfaces\GraphQLInputInterface;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

final class AuthenticateInput extends InputObjectType implements GraphQLInputInterface
{
    public function __construct(
        EmailScalar $emailScalar,
        PasswordScalar $passwordScalar
    ) {
        parent::__construct([
            "name" => "AuthenticateInput",
            "fields" => [
                "email" => [
                    "type" => Type::nonNull($emailScalar)
                ],
                "password" => [
                    "type" => Type::nonNull($passwordScalar)
                ]
            ]
        ]);
    }
}
