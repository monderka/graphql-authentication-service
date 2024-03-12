<?php

namespace App\GraphQL\Scalars;

use App\Exceptions\InvalidEmailException;
use App\Interfaces\GraphQLScalarInterface;
use GraphQL\Error\Error;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\ScalarType;

final class EmailScalar extends ScalarType implements GraphQLScalarInterface
{
    public string $name = 'EmailScalar';
    public ?string $description = 'This scalar type represents valid e-mail address. (String)';

    public function serialize($value)
    {
        return $value;
    }

    public function parseValue($value)
    {
        if ($value !== null && $value !== '' && ! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException();
        }

        return $value;
    }

    public function parseLiteral(Node $valueNode, array $variables = null)
    {
        if (! $valueNode instanceof StringValueNode) {
            $kind = $valueNode->kind ?? '';
            throw new Error('Can only parse strings got: ' . $kind, $valueNode);
        }

        if (! filter_var($valueNode->value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException();
        }

        return $valueNode->value;
    }
}
