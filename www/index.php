<?php

use Apitte\Core\Application\IApplication;
use App\Bootstrap;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/Bootstrap.php';

Bootstrap::boot()
    ->createContainer()
    ->getByType(IApplication::class)
    ->run();
