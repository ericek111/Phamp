<?php
$dummyPath = include __DIR__ . '/_checker_prep.php';

use Lixko\Phamp\Parser\Adif\AdifParser;

$parser = new AdifParser();

$handle = fopen($argv[2], 'r');
if ($handle) {
	while (($line = fgets($handle)) !== false) {
		$parser->pushLine($line);
	}
	
	fclose($handle);
} else {
	echo "Error loading the logfile.\n";
}

$myChecker = $dummyPath . '\\' . $argv[1];
$checker = new $myChecker($parser->getManager());
$checker->checkLog();
