<?php
namespace Lixko\Phamp\Parser\Cabrillo;

use DateTime;
use InvalidArgumentException;
use Lixko\Phamp\Log\Qso\Line;
use Lixko\Phamp\Log\Qso\Manager;

class CabrilloParser {
	
	protected Manager $manager;

	public function __construct() {
		$this->manager = new Manager();
	}

	/** @throws \InvalidArgumentException */
	public function pushLine(string $line): void {
		if (str_starts_with($line, 'QSO: ')) {
			$qso = $this->qsoFromLine($line);
			$this->manager->addQso($qso);
		}
	}

	/** @throws \InvalidArgumentException */
	protected function qsoFromLine(string $line): ?Line {
		if (!str_starts_with($line, 'QSO: ')) {
			throw new InvalidArgumentException("Not a QSO line!");
		}

		$parts = preg_split('/\s+/', $line);
		
		$qso = new Line(
			is_numeric($parts[1]) ? (float) $parts[1] : $parts[1],
			new DateTime($parts[3] . 'T' . $parts[4] . 'Z'),
			$parts[9],
			$parts[11] . ' ' . $parts[12],
		);

		$qso->originalLine = $line;

		return $qso;
	}

	public function getManager(): Manager {
		return $this->manager;
	}

}