<?php

namespace Lester\Health\Checks\Checks;

use Spatie\Health\Checks\Checks\PingCheck as Check;
use Spatie\Health\Checks\Result as DefaultResult;
use Lester\Health\Checks\Result;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Spatie\Health\Exceptions\InvalidCheck;
use Illuminate\Support\Facades\Http;

class PingCheck extends Check
{
	private $failIfRedirected = false;
	
	public function failIfRedirected()
	{
		$this->failIfRedirected = true;
		return $this;
	}
	
	public function run(): DefaultResult
	{
		if (is_null($this->url)) {
			throw InvalidCheck::urlNotSet();
		}
	
		try {
			$request = Http::timeout($this->timeout)
				->withHeaders($this->headers)
				->retry($this->retryTimes)
				->send($this->method, $this->url);
			
			if ($request->redirect() && $this->failIfRedirected) {	
				return $this->failedResult();
			}
	
			if (! $request->successful()) {
				return $this->failedResult();
			}
		} catch (Exception) {
			return $this->failedResult();
		}
	
		return DefaultResult::make()
			->ok()
			->shortSummary('Reachable');
	}
}