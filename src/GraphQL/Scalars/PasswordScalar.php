<?php

namespace App\GraphQL\Scalars;

use App\Exceptions\InvalidPasswordException;
use App\Interfaces\GraphQLScalarInterface;
use GraphQL\Error\Error;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\ScalarType;
use Nette\Utils\Validators;

final class PasswordScalar extends ScalarType implements GraphQLScalarInterface
{
    public const PASSWORD_PATTERN = '*(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z]).*';
    public const MIN_LENGTH = 8;
    public const MAX_LENGTH = 64;

    public string $name = 'PasswordScalar';
    public ?string $description = 'This scalar type represents validated strong password';

    public function serialize($value)
    {
        return $value;
    }

    public function parseValue($value)
    {
        if (
            !Validators::is($value, 'pattern:.' . self::PASSWORD_PATTERN) ||
            !Validators::is($value, 'string:' . self::MIN_LENGTH . '..' . self::MAX_LENGTH)
        ) {
            throw new InvalidPasswordException();
        }

        return $value;
    }

    public function parseLiteral($valueNode, ?array $variables = null)
    {
        if (! $valueNode instanceof StringValueNode) {
            $kind = $valueNode->kind ?? '';
            throw new Error('Can only parse strings got: ' . $kind, $valueNode);
        }

        if (
            !Validators::is($valueNode->value, 'pattern:.' . self::PASSWORD_PATTERN) ||
            !Validators::is($valueNode->value, 'string:' . self::MIN_LENGTH . '..' . self::MAX_LENGTH)
        ) {
            throw new InvalidPasswordException();
        }

        return $valueNode->value;
    }
}
