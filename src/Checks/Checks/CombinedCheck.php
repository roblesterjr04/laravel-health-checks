<?php

namespace Lester\Health\Checks\Checks;

use Lester\Health\Checks\Check;
use Lester\Health\Checks\Result;

class CombinedCheck extends Check
{
    
    private $checkIcons = [
        "ok" => "✅",
        "warning" => "⚠️",
        "failed" => "⛔️",
    ];
    
    protected $checks = [];
    
    public function run(): Result
    {
        $notOk = 0;
        $total = count($this->checks);

        $result = Result::make();
        $summary = [];
            
        foreach ($this->checks as $check) {
            $checkResult = $check->run();
            $status = (string)$checkResult->status;
            if ($status !== 'ok') $notOk++;
            $summary[] = $this->checkIcons[(string)$checkResult->status] . $checkResult->getShortSummary();
        }
        
        $result->shortSummary(implode("\n", $summary));

        if ($notOk == $total) return $result->failed();
        if ($notOk > 0) return $result->warning();

        return $result->ok();

    }
    
    public function using(array $checks): self
    {
        $this->checks += $checks;
        
        return $this;
    }

}
