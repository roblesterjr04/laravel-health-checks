<?php

namespace Tests;

use Lester\Health\Checks\Checks\PaperTrailCheck;
use Lester\Health\Checks\Checks\CombinedCheck;
use Lester\Health\Checks\Checks\ApiCheck;
use Tests\TestCase;
use Tests\TestTraits\MocksGuzzleClients;

class CombinedCheckTest extends TestCase
{
	use MocksGuzzleClients;
	
	public function testCombinedCheck()
	{
		
		$ptClient = $this->getMockPapertrailClient();
		$apClient = $this->getMockHealthClient();
		
		$check = CombinedCheck::new()
			->using([
				PaperTrailCheck::new()->onSystem('mysystem')->client($ptClient),
				ApiCheck::new()->client($apClient)->getCheck('Cache')->baseUri('myexample.com'),
				ApiCheck::new()->client($apClient)->getCheck('Cache')->baseUri('myexample.com'),
			]);
			
		$result = $check->run();
		
		// Should result in 2 oks and 1 failure, so the overall result will be a warning.
		
		$this->assertEquals($result->status, 'warning');
		
	}

}