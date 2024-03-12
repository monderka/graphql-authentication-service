<?php

namespace App\GraphQL\Inputs;

use App\GraphQL\Scalars\PasswordScalar;
use App\Interfaces\GraphQLInputInterface;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

final class FinishResetPasswordInput extends InputObjectType implements GraphQLInputInterface
{
    public function __construct(
        PasswordScalar $passwordScalar
    ) {
        parent::__construct([
            "name" => "FinishResetPasswordInput",
            "fields" => [
                "resetToken" => [
                    "type" => Type::nonNull(Type::string())
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
