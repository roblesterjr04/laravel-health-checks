<?php

namespace Lester\Health\Checks\Checks;

use Spatie\Health\Checks\Checks\PingCheck as Check;
use Spatie\Health\Checks\Result as DefaultResult;
use Lester\Health\Checks\Result;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Spatie\Health\Exceptions\InvalidCheck;
use Illuminate\Support\Facades\Http;

class SnapshooterCheck extends Check
{
	public function run(): Result
	{
		$result = Result::make();
		$key = config('health-exp.snapshooter.secret') ?: 'ss-secret';

		$jobs = Http::withToken($key)
			->get('https://api.snapshooter.com/v1/jobs')
			->json('data');

		$failedjobs = [];

		foreach ($jobs as $job) {
			$jobId = $job["id"];
			$backup = Http::withToken($key)
				->get("https://api.snapshooter.com/v1/jobs/{$jobId}/backups", [
					"sort" => "-completed_at"
				])
				->json("data")[0] ?? null;

			$status = $backup["status"] ?? "failed";

			if ($status != 'completed') {
				$failedjobs[] = "⛔️ [$status] " . ($job['name'] ?? 'BACKUP FAILED');
			}
		}

		if (count($failedjobs) > 0) {
			$result->shortSummary(implode("<br />", $failedjobs));
			return $result->failed();
		}

		return $result->ok();
	}

}
