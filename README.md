# Expand the reach of your health checks!

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rob-lester-jr04/laravel-health-expansion.svg)](https://packagist.org/packages/rob-lester-jr04/laravel-health-expansion)
[![PHP Composer](https://github.com/roblesterjr04/laravel-health-checks/actions/workflows/run-tests.yml/badge.svg)](https://github.com/roblesterjr04/laravel-health-checks/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/rob-lester-jr04/laravel-health-expansion.svg)](https://packagist.org/packages/rob-lester-jr04/laravel-health-expansion)

Using this package to add more health checks to [Spatie Health Checks for Laravel](https://packagist.org/packages/rob-lester-jr04/laravel-health-expansion).

Here's an example where we'll monitor errors in PaperTrail.

```php
// typically, in a service provider

use Spatie\Health\Facades\Health;
use Lester\Health\Checks\Checks\PaperTrailCheck;

Health::checks([
	PaperTrailCheck::new()
		->onSystem('my-system-1'),
]);
```

By default, this will report a failure if there are more than 20 errors in the default time frame (10 minutes). It will report a warning if there are more than 10, and it will report as ok if the error count is under 10. The thresholds can be changed with the following methods:

```php
...highCount();
...lowCount();

// Change time frame (in minutes)
...lastMinutes();

```

## Available Checks

This package also contains the following checks:

* MailgunDomainCheck
	* This check will get the status for your mailgun domain(s) so you can monitor if mailgun has flagged your deliverability.
* ApiCheck
	* This check allows you to get the laravel health from another application. Useful for creating a permenant dashboard screen that monitors all your other systems.
* PaperTrailCheck
	* Scans PaperTrail logs for a set number of errors in a set time frame.

### Using the MailGun domain check

First, set the API key in the `.env` file.

```php
	MAILGUN_SECRET=########
```

Then enable the check in the service provider

```php

use Spatie\Health\Facades\Health;
use Lester\Health\Checks\Checks\MailgunDomainCheck;

Health::checks([
	// ...
	
	MailgunDomainCheck::new()
		->domain('mg.example.com'),
		
	// ...
]);


```

## Testing

```bash
composer test
```
