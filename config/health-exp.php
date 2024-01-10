<?php

return [
	
	'mailgun' => [
		'secret' => env('MAILGUN_API', 'mg-secret'),
	],
	
	'papertrail' => [
		'secret' => env('PAPERTRAIL_API', 'pt-secret'),
	],
	
	'snapshooter' => [
		'secret' => env('SNAPSHOOTER_API', 'ss-secret'),
	],
	
	'github' => [
		'secret' => env('GITHUB_API', 'gh-secret'),
	],
	
];