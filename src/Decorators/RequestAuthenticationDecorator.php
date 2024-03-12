<?php

namespace App\Decorators;

use Apitte\Core\Decorator\IRequestDecorator;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\Services\IdentityProvider;

final class RequestAuthenticationDecorator implements IRequestDecorator
{
    public function __construct(private readonly IdentityProvider $identityProviderService)
    {
    }

    public function decorateRequest(ApiRequest $request, ApiResponse $response): ApiRequest
    {
        $this->identityProviderService->registerRequest($request);
        return $request;
    }
}
