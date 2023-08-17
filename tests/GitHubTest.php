<?php

namespace Tests;

use Lester\Health\Checks\Checks\GitHubCheck;
use Lester\Health\Checks\Checks\TerminalCheck;
use Tests\TestCase;

class GitHubTest extends TestCase
{
	
	public function testGithubCheck()
	{
		$check = GitHubCheck::new();
			
		$result = $check->run();
		
		$this->assertInstanceOf('Lester\Health\Checks\Result', $result);
		
	}
	
	public function testTerminalCheck()
	{
		$check = TerminalCheck::new();
			
		$result = $check->command('cd ~/GitHub/wotc && php artisan test')->run();
		
		$this->assertInstanceOf('Lester\Health\Checks\Result', $result);
	}

}