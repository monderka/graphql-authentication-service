<?php

namespace App\GraphQL\Inputs;

use App\GraphQL\Scalars\PasswordScalar;
use App\Interfaces\GraphQLInputInterface;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

final class ChangePasswordInput extends InputObjectType implements GraphQLInputInterface
{
    public function __construct(
        PasswordScalar $passwordScalar
    ) {
        parent::__construct([
            "name" => "ChangePasswordInput",
            "fields" => [
                "oldPassword" => [
                    "type" => Type::nonNull($passwordScalar)
                ],
                "newPassword" => [
                    "type" => Type::nonNull($passwordScalar)
                ],
                "verifyPassword" => [
                    "type" => Type::nonNull($passwordScalar)
                ]
            ]
        ]);
    }
}
