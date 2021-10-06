<?php
$srcPath = dirname(__DIR__) . '/src';
$nsPrefix = include $srcPath . '/autoloader.php';

$dummyPath = Lixko\Phamp\Checker\Impl\DummyChecker::class;
$dummyPath = substr($dummyPath, 0, strrpos($dummyPath, '\\'));

if ($argc < 3) {
	echo "Usage: $argv[0] (checker class name) (log file)\n";
	
	if (str_starts_with($dummyPath, $nsPrefix)) {
		// get a path relative to the src/ directory.
		$dummyPath = substr($dummyPath, strlen($nsPrefix) + 1);
	}
	
	echo "Available checkers:\n";
	
	$path = str_replace('\\', '/', $dummyPath);
	$path = $srcPath . '/' . $path;
	foreach (glob($path . '/*.php') as $file) {
		$checkerClass = basename($file, '.php');
		echo "\t$checkerClass\n";
	}
}

return $dummyPath;
