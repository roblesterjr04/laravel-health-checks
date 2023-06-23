<?php

class Cache
{
	public static function remember($key, $time, $callback)
	{
		return $callback();
	}
}