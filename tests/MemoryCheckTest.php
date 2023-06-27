<?php

namespace Tests;

use Lester\Health\Checks\Checks\MemoryCheck;
use PHPUnit\Framework\TestCase;
use SpoofsLaravelApp;
use TestTraits\MocksGuzzleClients;

class MemoryCheckTest extends TestCase
{
	use SpoofsLaravelApp;
	
	public function testMemoryCheck()
	{
		$check = MemoryCheck::new();
			
		$result = $check->run();
		
		$this->assertEquals($result->status, 'ok');
		
	}

}