<?php

namespace Lester\Health\Checks\Checks;

use Spatie\Health\Checks\Check;
use Lester\Health\Checks\Result;
use GuzzleHttp\Client;
use Carbon\Carbon;

class TerminalCheck extends Check
{
	private $timeout = 5;
	private $command = 'pwd';
	
	public function run(): Result
	{
		$out = "";
		$commandResult = $this->executeTerminal($this->command, $out, $out, $this->timeout);
		
		$result = Result::make();
		if ($commandResult > 0) return $result->failed("{$this->command} resulted in an error");
		
		return $result->ok();
	}
	
	public function command($command): self
	{
		$this->command = $command;
		return $this;
	}
	
	public function timeout($timeout): self
	{
		$this->timeout = $timeout;
		return $this;
	}
	
	public function executeTerminal($cmd, $stdin="", &$stdout = "", &$stderr = "", $timeout=false)
	{
		
		$pipes = array();
		$process = proc_open(
			$cmd,
			array(array('pipe','r'),array('pipe','w'),array('pipe','w')),
			$pipes
		);
		$start = time();
		$stdout = '';
		$stderr = '';
	
		if(is_resource($process))
		{
			stream_set_blocking($pipes[0], 0);
			stream_set_blocking($pipes[1], 0);
			stream_set_blocking($pipes[2], 0);
			fwrite($pipes[0], $stdin);
			fclose($pipes[0]);
		}
	
		while(is_resource($process))
		{
			//echo ".";
			$stdout .= stream_get_contents($pipes[1]);
			$stderr .= stream_get_contents($pipes[2]);
	
			if($timeout !== false && time() - $start > $timeout)
			{
				proc_terminate($process, 9);
				return 1;
			}
	
			$status = proc_get_status($process);
			if(!$status['running'])
			{
				fclose($pipes[1]);
				fclose($pipes[2]);
				proc_close($process);
				return $status['exitcode'];
			}
	
			usleep(100000);
		}
	
		return 1;
	}
	
}