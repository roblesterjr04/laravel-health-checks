<?php

namespace Tests;

use Lester\Health\Checks\Checks\MemoryCheck;
use Tests\TestCase;
use Tests\TestTraits\MocksGuzzleClients;

class MemoryCheckTest extends TestCase
{
	
	public function testMemoryCheck()
	{
		$check = MemoryCheck::new();
			
		$result = $check->run();
		
		$this->assertEquals($result->status, 'ok');
		
	}

}