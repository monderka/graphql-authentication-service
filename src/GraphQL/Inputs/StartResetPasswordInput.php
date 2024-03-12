<?php

namespace App\GraphQL\Inputs;

use App\GraphQL\Scalars\EmailScalar;
use App\Interfaces\GraphQLInputInterface;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

final class StartResetPasswordInput extends InputObjectType implements GraphQLInputInterface
{
    public function __construct(
        EmailScalar $emailScalar
    ) {
        parent::__construct([
            "name" => "StartResetPasswordInput",
            "fields" => [
                "email" => [
                    "type" => Type::nonNull($emailScalar)
                ]
            ]
        ]);
    }
}
