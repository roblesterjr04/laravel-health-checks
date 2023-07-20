<?php

namespace Lester\Health\Checks\Checks;

use Spatie\Health\Checks\Check;
use Lester\Health\Checks\Result;
use GuzzleHttp\Client;
use Carbon\Carbon;

class GitHubCheck extends TerminalCheck
{
	private $timeout = 5;
	
	public function run(): Result
	{
		$out = "";
		$commandResult = $this->executeTerminal('git fetch', "", $out, $out, $this->timeout);
		
		$result = Result::make();
		if ($commandResult > 0) return $result->failed("Unable to fetch repo from GitHub Remote");
		
		return $result->ok();
	}
	
	public function timeout($timeout): self
	{
		$this->timeout = $timeout;
		return $this;
	}
	
}