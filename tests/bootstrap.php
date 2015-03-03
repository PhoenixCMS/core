<?php

require __DIR__ . '/../vendor/autoload.php';

if (!class_exists('Tester\Assert')) {
	echo "Install Nette Tester using `composer update --dev`\n";
	exit(1);
}

Tester\Environment::setup();

function id($val) {
	return $val;
}

$tempDir = __DIR__ . '/files/temp';

// clear cache dir
if (is_dir($tempDir)) {
	$ignored = ['.', '..'];
	foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($tempDir)) as $fileName => $fileInfo) {
		if (!in_array($fileInfo->getFilename(), $ignored)) {
			if (is_dir($fileName)) {
				@rmdir($fileName);
			} else {
				@unlink($fileName);
			}
		}
	}
}

$configurator = new Nette\Configurator;
$configurator->setDebugMode(FALSE);
$configurator->setTempDirectory($tempDir);
$configurator->createRobotLoader()
	->addDirectory(__DIR__ . '/files/mock')
	->register();
$configurator->addConfig(__DIR__ . '/files/config/config.neon');
return $configurator->createContainer();
