<?php

namespace Tests;

use Lester\Health\Checks\Checks\MailgunDomainCheck;
use Lester\Health\Checks\Checks\PaperTrailCheck;
use Lester\Health\Checks\Checks\ApiCheck;
use PHPUnit\Framework\TestCase;
use SpoofsLaravelApp;
use TestTraits\MocksGuzzleClients;

class ResultTest extends TestCase
{
	use SpoofsLaravelApp, MocksGuzzleClients;
		
	public function testMailgunReturnsResult(): void
	{
		$fakedResponse = file_get_contents(__DIR__ . '/Fixtures/MailgunDomain.json');
		
		$check = MailgunDomainCheck::new()->fakedResponse($fakedResponse)->domain('mg.example.com');
			
		$result = $check->run();
		
		$this->assertInstanceOf('Lester\Health\Checks\Result', $result);
	}
	
	public function testPapertrailReturnsResult(): void
	{
		$fakedResponse = file_get_contents(__DIR__ . '/Fixtures/PapertrailLogs.json');
		
		$client = $this->getMockClientWithResponse($fakedResponse);
		
		$check = PaperTrailCheck::new()->client($client)->onSystem('system1');
		
		$result = $check->run();
		
		$this->assertInstanceOf('Lester\Health\Checks\Result', $result);
		
	}
	
	public function testApiRequestResult(): void
	{
		$fakedResponse = file_get_contents(__DIR__ . '/Fixtures/ApiResults.json');
		$client = $this->getMockClientWithResponse($fakedResponse);
		
		$check = ApiCheck::new()->client($client);
			
		$result = $check->run();
		
		$this->assertInstanceOf('Lester\Health\Checks\Result', $result);
	}
	
	
}