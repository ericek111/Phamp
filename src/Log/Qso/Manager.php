<?php
namespace Lixko\Phamp\Log\Qso;

class Manager {

	/** @var Line[][] */
	protected array $qsos = [];

	public function addQso(Line $line) {
		$this->qsos[$line->call] ??= [];
		$this->qsos[$line->call][] = $line;
	}

	public function getQsosByCall(string $call): array {
		return $this->qsos[$call] ?? [];
	}

	public function getAllQsosGrouped(): array {
		return $this->qsos;
	}

}