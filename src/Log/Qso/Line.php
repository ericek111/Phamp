<?php
namespace Lixko\Phamp\Log\Qso;

use InvalidArgumentException;

class Line {
	
	public ?string $originalLine = null;
	
	protected array $kv = [];

	public function __construct(
		public float|string $frequency,
		public \DateTime $time,
		public string $call,
		public string $exch,
		
	) {
		$this->call = strtoupper($call);
		$this->exch = strtoupper($exch);

		if (is_float($frequency) && $frequency < 0) {
			throw new InvalidArgumentException('Negative frequency given.');
		}
	}
	
	public function setMeta(string $key, mixed $data): void {
		$this->kv[$key] = $data;
	}
	
	public function getMeta(string $key): mixed {
		return $this->kv[$key];
	}
	
	public function getAllMeta(): array {
		return $this->kv;
	}
	
	public function setAllMeta(array $meta): void {
		$this->kv = $meta;
	}

}