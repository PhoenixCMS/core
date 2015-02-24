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

$configurator = new Nette\Configurator;
$configurator->setDebugMode(FALSE);
$configurator->setTempDirectory(__DIR__ . '/files/temp');
$configurator->createRobotLoader()
	->addDirectory(__DIR__ . '/files/mock')
	->register();
$configurator->addConfig(__DIR__ . '/files/config/config.neon');
return $configurator->createContainer();
