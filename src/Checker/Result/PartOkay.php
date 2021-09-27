<?php
namespace Lixko\Phamp\Checker\Result;

class PartOkay extends PartCheckResult {
	
	public function toMsg(): string {
		return "$this->type is OK: $this->data\n";
	}
	
}
