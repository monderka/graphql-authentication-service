<?php

namespace App\Interfaces;

interface GraphQLResolverInterface
{
    public function resolve(array $parameters, array $options = []): array;
}
