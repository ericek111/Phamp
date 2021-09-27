<?php
namespace Lixko\Phamp\Checker\Result;

class PartWrong extends PartCheckResult {

	public function __construct(
		public string $type,
		public ?string $data = null,
		public string $reason,
		
	) {
		parent::__construct($type, $data);
	}

	public function toMsg(): string {
		return "$this->type is WRONG. $this->reason - $this->data\n";
	}

}