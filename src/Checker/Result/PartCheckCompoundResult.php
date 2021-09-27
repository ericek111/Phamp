<?php
namespace Lixko\Phamp\Checker\Result;

class PartCheckCompoundResult {
	
	/** @var PartCheckResult[] */
	protected array $partials = [];
	
	public function pushResult(PartCheckResult $result): void {
		$this->partials[] = $result;
	}

	public function hasErrors(): bool {
		foreach ($this->partials as $partial) {
			if ($partial instanceof PartWrong) {
				return true;
			}
		}
		return false;
	}

	public function isOkay(): bool {
		foreach ($this->partials as $partial) {
			if (! $partial instanceof PartOkay) {
				return false;
			}
		}
		return true;
	}

	public function getPartials(): array {
		return $this->partials;
	}

}