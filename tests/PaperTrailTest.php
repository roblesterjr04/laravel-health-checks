<?php

namespace Tests;

use Lester\Health\Checks\Checks\PaperTrailCheck;
use PHPUnit\Framework\TestCase;
use SpoofsLaravelApp;
use TestTraits\MocksGuzzleClients;

class PaperTrailTest extends TestCase
{
	use SpoofsLaravelApp, MocksGuzzleClients;
	
	public function testPaperTrailFailsGracefully(): void
	{
		$client = $this->getMockClient();
		
		$check = PaperTrailCheck::new()->client($client)->onSystem('system1');
		
		$result = $check->run();
		$this->assertEquals($result->status, 'ok');
		
		$result = $check->run();
		$this->assertEquals($result->status, 'warning');
	}
	
	public function testPaperTrailTriggersFailure(): void
	{
		$client = $this->getMockClient();
		
		$check = PaperTrailCheck::new()
			->highCount(1)
			->lastMinutes(9999999)
			->client($client)
			->onSystem('system1');
			
		$result = $check->run();
		$this->assertEquals($result->status, 'failed');
	}
	
	public function testPaperTrailNoErrorsInTime(): void
	{
		$client = $this->getMockClient();
		
		$check = PaperTrailCheck::new()
			->highCount(1)
			->lastMinutes(1)
			->client($client)
			->onSystem('system1');
			
		$result = $check->run();
		$this->assertEquals($result->status, 'ok');
	}
	
	private function getMockClient()
	{
		$fakedResponse = file_get_contents(__DIR__ . '/Fixtures/PapertrailLogs.json');
		return $this->getMockClientWithResponse($fakedResponse);
	}
	
}