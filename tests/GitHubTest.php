<?php

namespace Tests;

use Lester\Health\Checks\Checks\GitHubCheck;
use Lester\Health\Checks\Checks\GitHubActionsCheck;
use Lester\Health\Checks\Checks\TerminalCheck;
use Illuminate\Support\Facades\Http;
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
	
	public function testGithubActions()
	{
		
		Http::fake([
			'https://api.github.com/repos/*' => Http::response([
				'workflow_runs' => [
					[
						'name' => 'skip me',
					],
					[
						'name' => 'Test Flow',
						'status' => 'completed',
						'conclusion' => 'failure'
					]
				]
			]),
		]);
			
		$check = GitHubActionsCheck::new()
			->owner('tester')
			->repo('test')
			->action('Test Flow');
		
		$result = $check->run();
		$this->assertEquals($result->status, 'failed');
		
	}

}