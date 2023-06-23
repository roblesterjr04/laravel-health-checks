<?php

namespace TestTraits;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

trait MocksGuzzleClients
{
	public function getMockClientWithResponse($fakedResponse)
	{
		$mock = new MockHandler([
			new Response(200, [], $fakedResponse),
			new RequestException('Error Communicating with Server', new Request('GET', 'test')),
		]);
		
		$handlerStack = HandlerStack::create($mock);
		return new Client(['handler' => $handlerStack]);
	}
	
	public function getMockPapertrailClient()
	{
		$fakedResponse = file_get_contents(__DIR__ . '/../Fixtures/PapertrailLogs.json');
		return $this->getMockClientWithResponse($fakedResponse);
	}
	
	public function getMockHealthClient()
	{
		$fakedResponse = file_get_contents(__DIR__ . '/../Fixtures/ApiResults.json');
		return $this->getMockClientWithResponse($fakedResponse);
	}
}