<?php

namespace Lester\Health\Checks;

use Spatie\Health\Checks\Check as BaseCheck;

class Check extends BaseCheck
{
	public function run(): Result
	{
		return Result::make();
	}
}