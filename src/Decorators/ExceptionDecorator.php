<?php

namespace App\Decorators;

use Apitte\Core\Decorator\IErrorDecorator;
use Apitte\Core\Exception\Api\ClientErrorException;
use Apitte\Core\Exception\Api\ServerErrorException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use GraphQL\Error\Error;
use Monderka\JwtParser\InvalidWebTokenException;
use Nette\Utils\JsonException;
use Psr\Log\LoggerInterface;
use Throwable;
use Tracy\ILogger;
use TypeError;
use ValueError;

final class ExceptionDecorator implements IErrorDecorator
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function decorateError(ApiRequest $request, ApiResponse $response, Throwable $error): ApiResponse
    {
        if ($error instanceof ClientErrorException && $request->getMethod() === "OPTIONS") {
            return $this->cors($response);
        } elseif ($error instanceof ServerErrorException) {
            /** @var Throwable $exc */
            $exc = $error->getPrevious();
        } else {
            $exc = $error;
        }
        switch (true) {
            case $exc instanceof Error:
            case $exc instanceof InvalidWebTokenException:
                $httpCode = 200;
                $level = ILogger::EXCEPTION;
                break;
            case $exc instanceof TypeError:
            case $exc instanceof ValueError:
            case $exc instanceof JsonException:
                $level = ILogger::ERROR;
                $httpCode = 200;
                break;
            default:
                $level = ILogger::CRITICAL;
                $httpCode = 500;
                break;
        }
        if (!empty($level)) {
            $this->logger->log($exc, $level);
        }
        return $this->error($response, [
            "status" => "error",
            "code" => $exc->getCode(),
            "message" => $exc->getMessage(),
            "exception" => $exc::class
        ], $httpCode);
    }

    /** @param array{ "status": string, "code": int|mixed, "message": string, "exception": string } $data */
    private function error(ApiResponse $response, array $data, int $code): ApiResponse
    {
        $response->getBody();
        $response->rewindBody()->writeBody("                                         ");
        return $response->withStatus($code)
            ->withHeaders([
                "Access-Control-Allow-Origin" => "*",
                "Access-Control-Allow-Methods" => "POST, OPTIONS",
                "Access-Control-Allow-Headers" => "*"
            ])
            ->writeJsonBody($data);
    }

    private function cors(ApiResponse $response): ApiResponse
    {
        $body = str_repeat(" ", 120);
        return $response->withStatus(200)
            ->withHeaders([
                "Access-Control-Allow-Origin" => "*",
                "Access-Control-Allow-Methods" => "POST, OPTIONS",
                "Access-Control-Allow-Headers" => "*"
            ])
            ->rewindBody()
            ->writeBody($body);
    }
}
