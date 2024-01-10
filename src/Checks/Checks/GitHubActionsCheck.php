<?php

namespace Lester\Health\Checks\Checks;

use Lester\Health\Checks\Check;
use Lester\Health\Checks\Result;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class GitHubActionsCheck extends Check
{
	
	private $action;
	private $repo;
	private $owner;
	
	public function run(): Result
	{
		$result = Result::make();
		$response = Http::withToken(config('health-exp.github.secret') ?: '')
			->get("https://api.github.com/repos/{$this->owner}/{$this->repo}/actions/runs");
			
		$array = $response->json('workflow_runs');
		
		foreach ($array as $run) {
			if ($run['name'] === $this->action) {
				
				if ($run['status'] == 'in_progress') {
					$result->shortSummary('Test program running...');
					return $result->warning();
				} 
				
				if ($run['conclusion'] == 'failure') return $result->failed();
			}
		}
		
		return $result;
	}
	
	public function action($name)
	{
		$this->action = $name;
		return $this;
	}
	
	public function owner($owner)
	{
		$this->owner = $owner;
		return $this;
	}
	
	public function repo($repo)
	{
		$this->repo = $repo;
		return $this;
	}
	
}