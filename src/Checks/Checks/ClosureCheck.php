<?php

namespace Lester\Health\Checks\Checks;

use Lester\Health\Checks\Check;
use Lester\Health\Checks\Result;
use GuzzleHttp\Client;
use Cache;
use Closure;

class ClosureCheck extends Check
{
	private $callback;
	
	public function run(): Result
	{
		$result = Result::make();
		$callback = $this->callback;
		$callbackResult = $callback($this, $result);
		
		if ($callbackResult === true)
			return $result->ok();
		
		if ($callbackResult === false)
			return $result->failed();
		
		return $result;
	}
	
	public function with(Closure $callback): self
	{
		$this->callback = $callback;
		
		return $this;
	}
	
}