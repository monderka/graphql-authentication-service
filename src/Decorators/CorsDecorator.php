<?php

namespace App\Decorators;

use Apitte\Core\Decorator\IResponseDecorator;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

final class CorsDecorator implements IResponseDecorator
{
    public function decorateResponse(ApiRequest $request, ApiResponse $response): ApiResponse
    {
        return $response->withHeaders([
            "Access-Control-Allow-Origin" => "*",
            "Access-Control-Allow-Methods" => "POST, OPTIONS",
            "Access-Control-Allow-Headers" => "*"
        ]);
    }
}
