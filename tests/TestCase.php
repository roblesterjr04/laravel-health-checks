<?php

namespace Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Schema;
use Laravel\Horizon\Contracts\MasterSupervisorRepository;
use Laravel\Horizon\HorizonServiceProvider;
use Mockery;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Health\HealthServiceProvider;

class TestCase extends Orchestra
{
	protected function setUp(): void
	{
		parent::setUp();

		Factory::guessFactoryNamesUsing(
			fn (string $modelName) => 'Spatie\\Health\\Database\\Factories\\'.class_basename($modelName).'Factory'
		);
		
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
		
		if (!function_exists('trans')) {
			function trans($string)
			{
				return $string;
			}
		}
	}
}