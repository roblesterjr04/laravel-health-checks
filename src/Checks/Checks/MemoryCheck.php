<?php

namespace Lester\Health\Checks\Checks;

use Lester\Health\Checks\Check;
use Lester\Health\Checks\Result;

class MemoryCheck extends Check
{
	protected ?string $label = "Memory Usage";
	protected int $warningThreshold = 80;
	protected int $errorThreshold = 95;

	public function warnWhenUsedSpaceIsAbovePercentage(int $percentage): self
	{
		$this->warningThreshold = $percentage;

		return $this;
	}

	public function failWhenUsedSpaceIsAbovePercentage(int $percentage): self
	{
		$this->errorThreshold = $percentage;

		return $this;
	}

	public function run(): Result
	{
		$memoryUsage = $this->getMemoryUsage();

		$memoryUsedPercentage = $memoryUsage['percentage'];

		$result = Result::make()
			->meta(['percentage' => $memoryUsedPercentage])
			->shortSummary($memoryUsage['status']);

		if ($memoryUsedPercentage > $this->errorThreshold) {
			return $result->failed("The memory is almost full ({$memoryUsedPercentage}% used).");
		}

		if ($memoryUsedPercentage > $this->warningThreshold) {
			return $result->warning("The memory is almost full ({$memoryUsedPercentage}% used).");
		}

		return $result->ok();
	}

	protected function getMemoryUsage(): array
	{
		$memoryTotal = null;
		$memoryFree = null;
	
		if (stristr(PHP_OS, "win") && !stristr(PHP_OS, "Darwin")) {
			// Get total physical memory (this is in bytes)
			$cmd = "wmic ComputerSystem get TotalPhysicalMemory";
			@exec($cmd, $outputTotalPhysicalMemory);
	
			// Get free physical memory (this is in kibibytes!)
			$cmd = "wmic OS get FreePhysicalMemory";
			@exec($cmd, $outputFreePhysicalMemory);
	
			if ($outputTotalPhysicalMemory && $outputFreePhysicalMemory) {
				// Find total value
				foreach ($outputTotalPhysicalMemory as $line) {
					if ($line && preg_match("/^[0-9]+\$/", $line)) {
						$memoryTotal = $line;
						break;
					}
				}
	
				// Find free value
				foreach ($outputFreePhysicalMemory as $line) {
					if ($line && preg_match("/^[0-9]+\$/", $line)) {
						$memoryFree = $line;
						$memoryFree *= 1024;  // convert from kibibytes to bytes
						break;
					}
				}
			}
		}
		else if (stristr(PHP_OS, "Darwin")) {
			
			//This is purely for testing!!!!	
			
			$memoryFree = 7000 * 1024;
			$memoryTotal = 8000 * 1024;
			
		} else {
			if (is_readable("/proc/meminfo"))
			{
				$stats = @file_get_contents("/proc/meminfo");
	
				if ($stats !== false) {
					// Separate lines
					$stats = str_replace(array("\r\n", "\n\r", "\r"), "\n", $stats);
					$stats = explode("\n", $stats);
	
					// Separate values and find correct lines for total and free mem
					foreach ($stats as $statLine) {
						$statLineData = explode(":", trim($statLine));
	
						//
						// Extract size (TODO: It seems that (at least) the two values for total and free memory have the unit "kB" always. Is this correct?
						//
	
						// Total memory
						if (count($statLineData) == 2 && trim($statLineData[0]) == "MemTotal") {
							$memoryTotal = trim($statLineData[1]);
							$memoryTotal = explode(" ", $memoryTotal);
							$memoryTotal = $memoryTotal[0];
							$memoryTotal *= 1024;  // convert from kibibytes to bytes
						}
	
						// Free memory
						if (count($statLineData) == 2 && trim($statLineData[0]) == "MemAvailable") {
							$memoryFree = trim($statLineData[1]);
							$memoryFree = explode(" ", $memoryFree);
							$memoryFree = $memoryFree[0];
							$memoryFree *= 1024;  // convert from kibibytes to bytes
						}
					}
				}
			}
		}
	
		if (is_null($memoryTotal) || is_null($memoryFree)) {
			return [];
		} else {
			return [
				"total" => $memoryTotal,
				"free" => $memoryFree,
				"percentage" => round(100 - ($memoryFree * 100 / $memoryTotal)),
				'status' => sprintf("%s / %s (%s%%)",
					$this->getNiceFileSize($memoryTotal - $memoryFree),
					$this->getNiceFileSize($memoryTotal),
					round(100 - ($memoryFree * 100 / $memoryTotal))
				)
			];
		}
	}
	
	private function getNiceFileSize($bytes): string
	{
		$unit=array('B','KB','MB','GB','TB','PB');
		if ($bytes==0) return '0 ' . $unit[0];
		return @round($bytes/pow(1000,($i=floor(log($bytes,1000)))),2) .' '. ($unit[$i] ?? 'B');
	}
}