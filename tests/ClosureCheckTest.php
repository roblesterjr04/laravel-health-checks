<?php

namespace Tests;

use Lester\Health\Checks\Checks\ClosureCheck;
use Illuminate\Support\Facades\Http;
use Tests\TestTraits\MocksGuzzleClients;
use Tests\TestCase;

class ClosureCheckTest extends TestCase
{
	
	public function testClosureCheckFailed()
	{
		
		$check = ClosureCheck::new()->with(function($check, $result) {
			return false;
		});
		
		$result = $check->run();
		$this->assertEquals($result->status, 'failed');
		
	}
	
	public function testClosureCheckOk()
	{
		
		$check = ClosureCheck::new()->with(function($check, $result) {
			return true;
		});
		
		$result = $check->run();
		$this->assertEquals($result->status, 'ok');
		
	}
	
	public function testClosureCheckResult()
	{
		
		$check = ClosureCheck::new()->with(function($check, $result) {
			$result->warning();
		});
		
		$result = $check->run();
		$this->assertEquals($result->status, 'warning');
		
	}

}