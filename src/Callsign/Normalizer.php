<?php
namespace Lixko\Phamp\Callsign;

class Normalizer {
	
	public function collectCallVariants(array $calls): array {
		$variants = [];
		
		foreach ($calls as $call) {
			$base = $this->normalizeCallsign($call);
			$variants[$base] ??= [ $call ];
			
			if (!in_array($call, $variants[$base])) {
				$variants[$base][] = $call;
			}
		}
		
		// Let's keep it O(n), throw out calls with only one known form.
		$variants = array_filter($variants, fn($a) => count($a) > 1);
		
		return $variants;
	}
	
	public function normalizeCallsign(string $call): string {
		$parts = explode('/', $call);
		
		// CALL does not have a slash, hence does not have any secondary pre/suffixes.
		if (count($parts) < 2) {
			return $call;
		}
		
		// must have a number, exclude /QRP, /MM... one funny call sign: 4D71/N0NM
		$base = array_filter($parts, fn($part) => preg_match('/\d/', $part));
		
		// prefixes are usually shorter than the CALL (HB0/, /7), so let's strlen the parts
		$lengths = array_map(fn($a) => strlen($a), $base);
		
		// If two parts have the same length, the latter will be kept. This is not an issue, because
		// let's say in HB0/G3M/QRP, the /QRP will be stripped above and we care about the middle part.
		$base = array_combine($lengths, $base);
		
		// Reverse sort based on keys (lengths of values). The longest callsign-like value will be on top.
		krsort($base, SORT_NUMERIC);
		
		$base = reset($base);
		
		return $base;
	}
	
}