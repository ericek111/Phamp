<?php
namespace Lixko\Phamp\Log\Qso;

use InvalidArgumentException;

class Line {

	public ?string $originalLine = null;

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

}