<?php

namespace Ingresse\API\v1\Middleware;

use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use \Zend\Diactoros\Response\JsonResponse;

class Response implements MiddlewareInterface
{

	public function process(ServerRequestInterface $request, DelegateInterface $delegate)
	{
		$response = $request->getAttribute('response');

		return new JsonResponse(
			[
				'success' => $response['success'],
				'data' => $response['data'] ?? '',
				'message' => $response['message']
			], 
			$response['statusCode'] ?? 200,
			$response['headers'] ?? []
		);
    }
}