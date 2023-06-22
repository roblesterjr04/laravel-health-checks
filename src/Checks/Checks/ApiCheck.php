<?php

namespace Lester\Health\Checks\Checks;

use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use GuzzleHttp\Client;
use Cache;

class ApiCheck extends Check
{
    protected $baseUri;
    protected $headers = [];
    protected $path = 'api/health';
    protected $check;
    protected $cacheSeconds = 45;

    public function run(): Result
    {
        $result = Result::make();

        if (!$this->check) return $result->warning("No check name provided.");

        $checks = $this->getHealth();

        if (!$checks) return $result->failed("Could not get health check for {$this->check}");

        $check = collect($checks->checkResults)->filter(function($item) {
            return $item->name == $this->check;
        })->first();

        $status = $check->status;
        return $result
            ->notificationMessage($check->notificationMessage)
            ->shortSummary($check->shortSummary)
            ->meta((array)$check->meta)
            ->$status($check->notificationMessage);
    }
    
    public function withPath($path): self
    {
        $this->path = $path;
        
        return $this;
    }

    public function getCheck($name): self
    {
        $this->check = $name;

        return $this;
    }

    public function baseUri($baseUri): self
    {
        $this->baseUri = $baseUri;

        return $this;
    }

    public function withHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    protected function getHealth()
    {
        $client = new Client([
            'base_uri' => $this->baseUri,
            'headers' => $this->headers,
        ]);

        try {
            return Cache::remember("health_host_{$this->path}_{$this->baseUri}", now()->addSeconds($this->cacheSeconds), function() use ($client) {
                return json_decode($client->get($this->path)->getBody());
            });
        } catch (\Exception $e) {
            return false;
        }

    }
}
