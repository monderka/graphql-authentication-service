<?php

namespace App\GraphQL\Inputs;

use App\GraphQL\Scalars\EmailScalar;
use App\Interfaces\GraphQLInputInterface;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

final class UpdateCurrentUserInput extends InputObjectType implements GraphQLInputInterface
{
    public function __construct(
        EmailScalar $emailScalar
    ) {
        parent::__construct([
            "name" => "UpdateCurrentUserInput",
            "fields" => [
                "email" => [
                    "type" => $emailScalar
                ],
                "active" => [
                    "type" => Type::boolean()
                ]
            ]
        ]);
    }
}
