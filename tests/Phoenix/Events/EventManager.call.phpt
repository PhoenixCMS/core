<?php
use Nette\DI\Container;
use Phoenix\Events\EventManager;
use Phoenix\Events\InvalidEventArgumentValueException;
use Tester\Assert;

/** @var Container $container */
$container = require "../../bootstrap.php";
/** @var EventManager $eventManager */
$eventManager = $container->getByType('\\Phoenix\\Events\\EventManager');

Assert::equal(2, count($container->findByType('\\Phoenix\\Events\\IEventListener')));

$eventManager->call('onFoo', 1, 2, 3);

Assert::equal(TRUE, $container->getByType('\\FooListener')->called);
Assert::exception(function () use ($eventManager) {
    $eventManager->call('onFoo', 'a', 2, 3);
}, new InvalidEventArgumentValueException);
