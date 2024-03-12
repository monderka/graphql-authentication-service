<?php

namespace App;

use Nette\Bootstrap\Configurator;
use Tracy\Debugger;

class Bootstrap
{
    public const DISABLED_EXTENSIONS = [
        'forms',
        'database',
        'security',
        'session',
        'latte',
        'mail'
    ];

    public static function boot(): Configurator
    {
        $appDir = dirname(__DIR__);
        $configurator = new Configurator();
        $configurator->setTempDirectory($appDir . '/temp');
        $configurator->addConfig($appDir . '/config/config.neon');
        $configurator = self::addEnvParameters($configurator);

        foreach (self::DISABLED_EXTENSIONS as $disabledExtension) {
            unset($configurator->defaultExtensions[$disabledExtension]);
        }

        return $configurator;
    }

    private static function addEnvParameters(Configurator $configurator): Configurator
    {
        $configurator->setTimeZone(self::getEnvCol("BACKEND_TIMEZONE", "Europe/Prague"));

        $debugMode = (bool) self::getEnvCol("BACKEND_DEBUG_MODE", true);
        $configurator->setDebugMode($debugMode);

        $logDirectory = self::getEnvCol("BACKEND_LOG_DIRECTORY", dirname(__DIR__) . '/log');
        $configurator->enableTracy($logDirectory);
        Debugger::$showBar = false;

        $configurator->addStaticParameters([
            "rootDir" => dirname(__DIR__)
        ]);

        $parameters = [
            "db" => [
                "host" => self::getEnvCol("BACKEND_DB_HOST", "localhost"),
                "driver" => self::getEnvCol("BACKEND_DB_DRIVER", "pdo_mysql"),
                "dbname" => self::getEnvCol("BACKEND_DB_NAME", "test"),
                "user" => self::getEnvCol("BACKEND_DB_USER", "test"),
                "password" => self::getEnvCol("BACKEND_DB_PASSWORD", "test")
            ],
            "redis" => [
                "uri" => self::getEnvCol("BACKEND_REDIS_URI", "localhost")
            ],
            "jwt" => [
                "issuer" => self::getEnvCol("BACKEND_JWT_ISSUER", "Test"),
                "jwtAlgo" => self::getEnvCol("BACKEND_JWT_ALGO", "ES256"),
                "privateKeyPassPhrase" =>
                    self::getEnvCol("BACKEND_JWT_PRIVATE_KEY_PASSWORD", ""),
                "publicKeyPath" =>
                    self::getEnvCol("BACKEND_JWT_PUBLIC_KEY_PATH", __DIR__ . "/private/public.pem"),
                "privateKeyPath" =>
                    self::getEnvCol("BACKEND_JWT_PRIVATE_KEY_PATH", __DIR__ . "/private/private.pem"),
                "accessTokenExpiration" => (int) self::getEnvCol("BACKEND_JWT_EXPIRATION", 3600)
            ]
        ];
        $configurator->addDynamicParameters($parameters);

        return $configurator;
    }

    private static function getEnvCol(string $key, string|int|float|bool $replace): string|int|float|null
    {
        return empty(getenv($key)) ? $replace : getenv($key);
    }
}
