<?php

namespace Lester\Health\Checks\Checks;

use Lester\Health\Checks\Check;
use Lester\Health\Checks\Result;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Mailgun\Mailgun;

class MailgunDomainCheck extends Check
{
    protected $domain;
    protected $fakedResponse;

    public function run(): Result
    {
        $result = Result::make();
            
        $mg = Mailgun::create(config('health-exp.mailgun.secret') ?: '');

        try {

            if ($this->fakedResponse) {
                $domains = json_decode($this->fakedResponse);
                $status = $domains->domain->state;
            } else {
                $domains = $mg->domains()->show($this->domain);
                $status = $domains->getDomain()->getState();
            }
            
            if ($status == 'active') {
                return $result->shortSummary("{$this->domain} is ok")->ok();
            } else {
                return $result->warning("{$this->domain} is $status");
            }

        } catch (\Exception $e) {
            return $result->failed("Cannot get mailgun domain status for {$this->domain}");
        }

        return $result->ok();

    }

    public function domain($domain): self
    {
        $this->domain = $domain;

        return $this;
    }
    
    public function fakedResponse($json)
    {
        $this->fakedResponse = $json;
        
        return $this;
    }
}
