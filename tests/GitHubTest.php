<?php

namespace Tests;

use Lester\Health\Checks\Checks\GitHubCheck;
use Lester\Health\Checks\Checks\TerminalCheck;
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
	
	public function testTerminalCheck()
	{
		$check = TerminalCheck::new();
			
		$result = $check->command('ls')->run();
		
		$this->assertInstanceOf('Lester\Health\Checks\Result', $result);
	}

}