<?php
namespace Lixko\Phamp\Checker\Impl;

use Lixko\Phamp\Checker\ILogChecker;

/**
 * Checker that always returns `true`. no matter what.
 *
 * @package Lixko\Phamp\Checker\Impl
 */
class DummyChecker implements ILogChecker {
	
	public function checkLog(): bool {
		return true;
	}
	
}