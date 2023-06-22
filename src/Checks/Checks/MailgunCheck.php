<?php

namespace Lester\Health\Checks\Checks;

use Lester\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Mailgun\Mailgun;

class MailgunCheck extends Check
{
    protected $domain;

    public function run(): Result
    {

        $result = Result::make();

        $mg = Mailgun::create(config('health-exp.mailgun.secret'));

        try {

            $domains = $mg->domains()->show($this->domain);
            $status = $domains->getDomain()->getState();

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
}
