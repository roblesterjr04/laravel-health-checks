<?php

namespace Lester;

use Illuminate\Support\ServiceProvider;

class HealthServiceProvider extends ServiceProvider
{
	const CONFIG_PATH = __DIR__ . '/../config/health-exp.php';
	
	public function boot()
	{
		$this->publishes([
			self::CONFIG_PATH => config_path('health-exp.php'),
		], 'config');
	}
	
	public function register()
	{
		$this->mergeConfigFrom(
			self::CONFIG_PATH,
			'health-exp'
		);
	}
}