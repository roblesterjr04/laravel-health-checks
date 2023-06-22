<?php

use Carbon\Carbon;

trait SpoofsLaravelApp
{
	public static function setUpBeforeClass(): void
	{
		if (!function_exists('app')) {
			function app($class) {
				return new $class;
			}
		}
		
		if (!function_exists('config')) {
			function config($key) {
				return $key;
			}
		}
		
		if (!function_exists('now')) {
			function now()
			{
				return Carbon::now();
			}
		}
		
	}
}