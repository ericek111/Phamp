<?php
namespace Lixko\Phamp\Checker\Result;

abstract class PartCheckResult {
	
	public function __construct(
		public string $type,
		public ?string $data = null,
	) {
	}

	abstract public function toMsg(): string;

}
