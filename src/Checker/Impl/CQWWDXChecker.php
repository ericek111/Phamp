<?php
namespace Lixko\Phamp\Checker\Impl;

use Lixko\Phamp\Callsign\Normalizer;
use Lixko\Phamp\Checker\ILogChecker;
use Lixko\Phamp\Checker\Result\PartCheckCompoundResult;
use Lixko\Phamp\Checker\Result\PartCheckResult;
use Lixko\Phamp\Checker\Result\PartOkay;
use Lixko\Phamp\Checker\Result\PartWrong;
use Lixko\Phamp\Log\Qso\Line;
use Lixko\Phamp\Log\Qso\Manager;

class CQWWDXChecker implements ILogChecker {
	
	protected Normalizer $normalizer;
	
	public const STATES = [
		"DX",
		"AL", "AK", "AZ", "AR", "CA", "CO", "CT", "DE", "DC", "FL", "GA", "HI", "ID", "IL", "IN", "IA", "KS", "KY", "LA", "ME", "MT", "NE", "NV", "NH", "NJ", "NM", "NY", "NC", "ND", "OH", "OK", "OR", "MD", "MA", "MI", "MN", "MS", "MO", "PA", "RI", "SC", "SD", "TN", "TX", "UT", "VT", "VA", "WA", "WV", "WI", "WY",
		"AB", "BC", "MB", "NB", "NL", "NT", "NS", "NU", "ON", "PE", "QC", "SK", "YT",
		"NF",
	];

	public function __construct(
		protected Manager $manager,
	) {
		$this->normalizer = new Normalizer();
	}

	public function checkLog(): bool {
		$qsosGrouped = $this->manager->getAllQsosGrouped();
		foreach ($qsosGrouped as $call => $qsos) {
			$entries = [];
			foreach ($qsos as $qso) {
				$compoundResult = new PartCheckCompoundResult();

				$exchResult = $this->checkExch($qso);
				$compoundResult->pushResult($exchResult);

				$entries[] = [
					'qso' => $qso,
					'compound' => $compoundResult,
				];
			}

			$this->examineCompoundResults($entries);
		}

		$calls = array_keys($qsosGrouped);
		$variants = $this->normalizer->collectCallVariants($calls);
		$this->examineCallVariants($variants);

		return true;
	}

	protected function checkExch(Line $qso): PartCheckResult {
		$state = substr($qso->exch, strpos($qso->exch, ' ') + 1);

		// TODO: Differentiate between US/CA/other stations.
		if (!in_array($state, static::STATES)) {
			return new PartWrong('exch', $state, "This state does not exist!");
		}

		$qsosForCall = $this->manager->getQsosByCall($qso->call);

		foreach ($qsosForCall as $other) {
			if ($other->exch !== $qso->exch) {
				return new PartWrong('exch', $qso->exch, "Several different exchanges logged!");
			}
		}

		return new PartOkay('exch', $qso->exch);
	}

	protected function examineCompoundResults(array $entries): void {
		if (count($entries) === 0) {
			return;
		}

		if (!array_reduce($entries, fn($carry, $item) => $carry || ! $item['compound']->isOkay())) {
			// everything's all right
			return;
		}

		$call = reset($entries)['qso']->call;

		$types = [];
		foreach ($entries as $entry) {
			foreach ($entry['compound']->getPartials() as $partial) {
				$types[$partial->type] ??= [];
				$types[$partial->type][] = $partial;
			}
		}

		foreach ($types as $type => $partials) {
			$onlyWrongs = array_filter($partials, fn(PartCheckResult $partial) => $partial instanceof PartWrong);
			
			// for this type of result, there are only "wrong" entries and they all have the same `reason`
			if (count($onlyWrongs) === count($partials)
				&& count(array_unique(array_map(fn(PartCheckResult $partial) => $partial->reason, $onlyWrongs))) === 1) {
				echo "$call: Common error for $type: " . reset($partials)->reason . " - ";
				echo implode(', ', array_map(fn($partial) => $partial->data, $partials));
				echo "\n";

				unset($types[$type]);
			}
		}

		foreach ($entries as $entry) {
			/** @var Line $qso */
			$qso = $entry['qso'];
			/** @var PartCheckCompoundResult $compound */
			$compound = $entry['compound'];
			$partials = $compound->getPartials();

			if (!$compound->isOkay()) {
				echo $qso->originalLine;

				$notDone = array_filter($partials, fn(PartCheckResult $partial) => isset($types[$partial->type]));
				foreach ($notDone as $partial) {
					echo "\t" . $partial->toMsg() . "\n";
				}
			}
		}

		echo "\n";
	}
	
	protected function examineCallVariants(array $variants): void {
		foreach ($variants as $base => $other) {
			echo "$base: Found prefixed variants of the callsign: " . implode(', ', $other) . "\n";
			
			$qsos = array_map(fn($call) => $this->manager->getQsosByCall($call), $other);
			array_walk_recursive($qsos, function(Line $qso) {
				echo $qso->originalLine;
			});
		}
	}
	
}