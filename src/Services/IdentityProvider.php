<?php

namespace App\Services;

use Apitte\Core\Http\ApiRequest;
use App\Interfaces\ServiceInterface;
use App\Security\Identity;
use Monderka\JwtParser\InvalidWebTokenException;
use Monderka\JwtParser\WebTokenParser;

final class IdentityProvider implements ServiceInterface
{
    private string $clientIpAddress = "";
    private ?string $accessToken = null;
    private ?Identity $identity = null;
    private string $jwtIssuer;

    public function __construct(
        private readonly WebTokenParser $webTokenParser
    ) {
    }

    public function setJwtIssuer(string $jwtIssuer): self
    {
        $this->jwtIssuer = $jwtIssuer;
        return $this;
    }

    public function setAccessToken(?string $accessToken): self
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    public function getClientIpAddress(): string
    {
        return $this->clientIpAddress;
    }

    public function registerRequest(ApiRequest $request): self
    {
        /** @var string $ipAddress */
        $ipAddress = $request->getServerParams()["REMOTE_ADDR"];
        $this->clientIpAddress = $ipAddress;
        if ($request->hasHeader("authorization")) {
            $this->accessToken = $request->getHeader("authorization")[0];
        }
        return $this;
    }

    /** @throws InvalidWebTokenException */
    public function getIdentity(): Identity
    {
        if (empty($this->accessToken)) {
            throw new InvalidWebTokenException();
        }
        if ($this->identity === null) {
            $payload = $this->webTokenParser->parse($this->accessToken, $this->jwtIssuer);
            $this->identity = new Identity((int) $payload["sub"], $payload["name"]);
        }
        return $this->identity;
    }
}
