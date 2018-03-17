<?php
namespace Ingresse\Handler;

use Psr\Log\LoggerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class MonologErrorListener
{
	private $logger;

	const LOG_FORMAT = '%d [%s] %s: %s';

	public function __construct(LoggerInterface $logger)
	{
		$this->logger = $logger;
	}

	public function __invoke($error, ServerRequestInterface $request, ResponseInterface $response)
	{
		$this->logger->error(sprintf(
            self::LOG_FORMAT,
            $response->getStatusCode(),
            $request->getMethod(),
            (string) $request->getUri(),
            $error->getMessage()
        ));
	}
}