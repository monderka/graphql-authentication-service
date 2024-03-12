<?php

namespace App\Interfaces;

interface GraphQLColumnResolverInterface
{
    public function resolve(): callable;
}
