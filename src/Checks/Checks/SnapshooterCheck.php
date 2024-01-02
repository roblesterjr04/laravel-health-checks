<?php

namespace Checks\Checks;

use Spatie\Health\Checks\Checks\PingCheck as Check;
use Spatie\Health\Checks\Result as DefaultResult;
use Lester\Health\Checks\Result;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Spatie\Health\Exceptions\InvalidCheck;
use Illuminate\Support\Facades\Http;

class SnapshooterCheck extends Check
{
	private $timeout = 5;
	
	public function run(): Result
	{
		$key = config('health-exp.snapshooter.secret') ?: 'ss-secret';
		
		$jobs = Http::withToken($key)
			->get('https://api.snapshooter.com/v1/jobs')
			->json('data');
			
		foreach ($jobs as $job) {
			$jobId = $job["id"];
			$backup = Http::withToken($key)
				->get("https://api.snapshooter.com/v1/jobs/{$jobId}/backups", [
					"sort" => "-completed_at"
				])
				->json("data")[0] ?? null;
				
			$status = $backup["status"] ?? "failed";
			
			if ($status != 'complete') {
				return $this->failedResult();
			}
		}
		
		return Result::make()->ok();
	}
	
}