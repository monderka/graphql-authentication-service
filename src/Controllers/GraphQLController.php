<?php

namespace App\Controllers;

use Apitte\Core\Annotation\Controller as Apitte;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\UI\Controller\IController;
use App\Services\GraphQLLogger;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Portiny\GraphQL\GraphQL\RequestProcessor;
use Portiny\GraphQL\Http\Request\JsonRequestParser;

#[Apitte\Path("/graphql")]
final class GraphQLController implements IController
{
    public function __construct(
        private readonly RequestProcessor $requestProcessor,
        private readonly GraphQLLogger $logger
    ) {
    }

    /** @throws JsonException */
    #[Apitte\Path("/")]
    #[Apitte\Method("POST")]
    public function index(ApiRequest $request, ApiResponse $response): ApiResponse
    {

        $res = $this->requestProcessor->process(
            requestParser: new JsonRequestParser($request->getBody()),
            logger: $this->logger
        );

        return $response
            ->writeBody(Json::encode($res))
            ->withStatus(200);
    }
}
