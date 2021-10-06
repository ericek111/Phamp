<?php
namespace Lixko\Phamp\Parser\Adif;

use DateTime;
use InvalidArgumentException;
use Lixko\Phamp\Log\Qso\Line;
use Lixko\Phamp\Log\Qso\Manager;

class AdifParser {
	
	protected Manager $manager;
	protected bool $hasHeader = false;
	protected string $buf = '';
	
	public function __construct() {
		$this->manager = new Manager();
	}
	
	/** @throws \InvalidArgumentException */
	public function pushLine(string $line): void {
		// TODO: This can be made more efficiently by reading char-by-char from the file itself (SplFileObject).
		// But meh, most ADIFs are one line per QSO anyway.
		if (empty($line)) {
			return;
		}
		
		if (!$this->hasHeader) {
			$eohpos = stripos($line, '<eoh>');
			if ($eohpos === false) {
				$this->buf .= $line;
			}
			if (strlen($line) > strlen('<eoh>') + 1) {
				$this->hasHeader = true;
				$before = substr($line, 0, $eohpos);
				$after = substr($line, $eohpos + strlen('<eoh>'), strlen($line));
				$this->processHeader($this->buf . $before);
				$this->buf = '';
				$this->pushLine($after);
				return;
			}
		}
		
		$eorpos = stripos($line, '<eor>');
		if ($eorpos === false) {
			$this->buf .= $line;
			return;
		}
		
		$line = $this->buf . $line;
		$this->buf = '';
		
		$eorpos = stripos($line, '<eor>');
		$before = substr($line, 0, $eorpos);
		
		$qso = $this->qsoFromLine($before);
		if ($qso) {
			$this->getManager()->addQso($qso);
		}
		
		$after = substr($line, $eorpos + strlen('<eor>'), strlen($line));
		$this->pushLine($after);
	}
	
	/** @throws \InvalidArgumentException */
	protected function qsoFromLine(string $line): ?Line {
		$lineLen = strlen($line);
		$marker = 0;
		$tagName = '';
		$data = [];
		
		for ($i = 0; $i < $lineLen; $i++) {
			// start of a tag
			if ($line[$i] === '<') {
				$marker = $i + 1;
			} else if ($line[$i] === ':') {
				$tagName = strtolower(substr($line, $marker, $i - $marker));
				$marker = $i + 1;
			} else if ($line[$i] === '>') {
				// is a tag without specified length -- should not happen in a valid ADIF
				if ($tagName === '') {
					throw new InvalidArgumentException("Tag ended before length was specified.");
				} else { // after :
					$valueLen = (int) substr($line, $marker, $i - $marker);
					$value = substr($line, $i + 1, $valueLen);
					
					$data[$tagName] = $value;
					
					$i += $valueLen;
				}
				$marker = $i + 1;
			}
		}
		
		$reqFields = ['freq', 'time_on', 'call', 'qso_date'];
		foreach ($reqFields as $reqField) {
			if (!isset($data[$reqField])) {
				throw new InvalidArgumentException("Field $reqField missing!");
			}
		}
		
		$qso = new Line(
			AdifUtil::megahertzToKilohertz($data['freq']),
			DateTime::createFromFormat('YmdHis', $data['qso_date'] . $data['time_on']),
			$data['call'],
			$data['stx'] ?? $data['cqz'] ?? $data['rst_send'] ?? '',
		);
		
		$qso->originalLine = $line;
		$qso->setAllMeta($data);
		
		return $qso;
	}
	
	public function processHeader(string $header): void {
		$this->hasHeader = true;
	}
	
	public function getManager(): Manager {
		return $this->manager;
	}
	
}