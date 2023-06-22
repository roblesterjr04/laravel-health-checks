<?php

namespace Lester\Health\Checks\Checks;

use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use GuzzleHttp\Client;
use Carbon\Carbon;

class PaperTrailCheck extends Check
{
    const PAPERTRAIL_ENDPOINT = 'https://papertrailapp.com/api/v1/events/search.json';
    protected $system;
    protected $query;
    
    protected $highCount = 20;
    protected $lowCount = 10;
    protected $lastMinutes = 10;

    public function onSystem($system): self
    {
        $this->system = $system;

        return $this;
    }
    
    public function query($query): self
    {
        $this->query = $query;
        
        return $this;
    }

    public function run(): Result
    {
        $result = Result::make();

        $lastMinutes = $this->lastMinutes;

        $client = new Client([
            'headers' => [
                'X-Papertrail-Token' => config('health-exp.papertrail.secret'),
            ]
        ]);

        try {
            $events = json_decode($client->get('https://papertrailapp.com/api/v1/events/search.json', [
                'query' => [
                    'limit' => 100,
                    'q' => "{$this->query} system:{$this->system}"
                ]
            ])->getBody());
        } catch (\Exception $e) {
            return $result->warning("Cannot get papertrail events");
        }

        $events = collect($events->events)->filter(function($event) use ($lastMinutes) {
            $date = new Carbon($event->received_at, '+00:00');
            return $date->gt(now()->subMinutes($lastMinutes));
        });

        $count = $events->count();
        if ($count > $this->highCount) {
            return $result->failed("High error count ($count). Review logs!");
        }

        if ($count > $this->lowCount) {
            return $result->warning("Warning error count ($count). Review logs!");
        }

        return $result->shortSummary("$count errors in last $lastMinutes minutes.")->ok();

    }
}
