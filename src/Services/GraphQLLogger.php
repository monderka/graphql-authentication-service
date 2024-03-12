<?php

namespace App\Services;

use App\Interfaces\ServiceInterface;
use Psr\Log\LoggerInterface;
use Stringable;
use Tracy\Debugger;
use Tracy\ILogger;

final class GraphQLLogger implements LoggerInterface, ServiceInterface
{
    public function __construct()
    {
    }

    public function emergency(Stringable|string $message, array $context = []): void
    {
        $this->log("emergency", $message);
    }

    public function alert(Stringable|string $message, array $context = []): void
    {
        $this->log("alert", $message);
    }

    public function critical(Stringable|string $message, array $context = []): void
    {
        $this->log("critical", $message);
    }

    public function error(Stringable|string $message, array $context = []): void
    {
        $this->log("error", $message);
    }

    public function warning(Stringable|string $message, array $context = []): void
    {
        $this->log("warning", $message);
    }

    public function notice(Stringable|string $message, array $context = []): void
    {
        $this->log("notice", $message);
    }

    public function info(Stringable|string $message, array $context = []): void
    {
        $this->log("info", $message);
    }

    public function debug(Stringable|string $message, array $context = []): void
    {
        $this->log("debug", $message);
    }

    public function log($level, Stringable|string $message, array $context = []): void
    {
        assert(is_scalar($level));
        if ($level === "notice" || $level === "debug" || $level === "inf") {
            $this->debugLog((string) $level, $message);
        } else {
            $this->errorLog((string) $level, $message);
        }
    }

    /**
     * @param string $level
     * @param Stringable|string $message
     * @return void
     */
    private function debugLog(string $level, Stringable|string $message): void
    {
    }

    /**
     * @param string $level
     * @param Stringable|string $message
     * @return void
     */
    private function errorLog(string $level, Stringable|string $message): void
    {
        $tracyLevel = match ($level) {
            default => ILogger::CRITICAL,
            "alert" => ILogger::EXCEPTION,
            "error" => ILogger::ERROR,
            "warning" => ILogger::WARNING
        };
        Debugger::log($message, $tracyLevel);
    }
}
