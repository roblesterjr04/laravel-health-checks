<?php

namespace Lester\Health\Checks;

use Spatie\Health\Checks\Check as BaseCheck;
use Spatie\Health\Checks\Result;

class Check extends BaseCheck
{
	public function run(): Result
	{
		return Result::make();
	}
}