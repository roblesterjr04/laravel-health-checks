<?php

namespace Tests;

use Lester\Health\Checks\Checks\PingCheck;
use Illuminate\Support\Facades\Http;
use Tests\TestTraits\MocksGuzzleClients;
use Tests\TestCase;

class PingCheckTest extends TestCase
{
	
	public function testPingCheck()
	{
		
		Http::fake([
			'fake.com' => Http::response('ok', 200),
		]);
			
		$check = PingCheck::new()->url('http://fake.com');
		
		$result = $check->run();
		$this->assertEquals($result->status, 'ok');
		
	}
	
	public function testPingRedirectCheck()
	{
		
		Http::fake([
			'fake.com' => Http::response('ok', 301, [
				'Location' => 'fake2.com',
			]),
			'fake2.com' => Http::response('ok', 200),
		]);
			
		$check = PingCheck::new()->failIfRedirected()->url('http://fake.com');
		
		$result = $check->run();
		$this->assertEquals($result->status, 'ok');
		
	}

}