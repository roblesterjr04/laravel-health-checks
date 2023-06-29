<?php

namespace Tests;

use Lester\Health\Checks\Checks\GitHubCheck;
use PHPUnit\Framework\TestCase;
use SpoofsLaravelApp;

class GitHubTest extends TestCase
{
	use SpoofsLaravelApp;
	
	public function testGithubCheck()
	{
		$check = GitHubCheck::new();
			
		$result = $check->run();
		
		$this->assertInstanceOf('Lester\Health\Checks\Result', $result);
		
	}

}