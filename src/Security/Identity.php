<?php

namespace App\Security;

final class Identity
{
    public function __construct(
        public readonly int $id,
        public readonly string $name
    ) {
    }
}
