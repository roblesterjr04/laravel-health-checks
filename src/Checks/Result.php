<?php

namespace Lester\Health\Checks;

use Spatie\Health\Checks\Result as BaseResult;
use Spatie\Health\Enums\Status;

class Result extends BaseResult
{	
	public static function make(string $message = ''): self
	{
		return new self(Status::ok(), $message);
	}
}