<?php

namespace Lixko\Phamp\Parser\Adif;

class AdifUtil {
	
	/**
	 * Converts a numeric string of arbitrary precision in megahertz (3.756) to float in kilohertz.
	 * This is a helper to prevent doing floating point math -- it simply multiplies the number by x1000.
	 *
	 * @param string $m
	 * @return float
	 */
	public static function megahertzToKilohertz(string $m): float {
		$m .= '000';
		
		$dot = strpos($m, '.');
		if ($dot === false) {
			return (float) $m;
		}
		
		$before = str_replace('.', '', substr($m, 0, $dot + 4));
		$after = substr($m, $dot + 4);
		$ret = $before . '.' . $after;
		
		return (float) $ret;
	}
	
}