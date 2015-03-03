<?php
/** @var \Nette\DI\Container $container */
$container = require "../../bootstrap.php";

use Phoenix\Events\EventManager;
use Phoenix\Events\InvalidEventArgumentTypeException;
use Tester\Assert;

class DummyString {

    public function a()
    {

    }

    public function __toString()
    {
        return 'def';
    }
}

function foo () {

}

/** @var EventManager $eventManager */
$eventManager = $container->getByType('\\Phoenix\\Events\\EventManager');

/** @var VariableTypesListener $listener */
$listener = $container->getByType('VariableTypesListener');

// null
$eventManager->call('onNull', NULL);
Assert::true($listener->onNullCalled);

// bool
$eventManager->call('onBool', TRUE, TRUE);
$eventManager->call('onBool', 1, 1);
$eventManager->call('onBool', "a", "b");
$eventManager->call('onBool', [], new stdClass);
Assert::true($listener->onBoolCalled);

// int
$eventManager->call('onInt', 1, 2, 3);
$eventManager->call('onInt', '1', '2', '3');
Assert::true($listener->onIntCalled);

// float
$eventManager->call('onFloat', 1.0, 1.1);
$eventManager->call('onFloat', '1.0', '1.1');
Assert::true($listener->onFloatCalled);

// string
$eventManager->call('onString', 'abc');
$eventManager->call('onString', new DummyString);
Assert::true($listener->onStringCalled);

// scalar
$eventManager->call('onScalar', TRUE);
$eventManager->call('onScalar', 1);
$eventManager->call('onScalar', 1.5);
$eventManager->call('onScalar', 'abc');
Assert::true($listener->onScalarCalled);

// array
$eventManager->call('onArray', []);
$eventManager->call('onArray', [1,2,3]);
Assert::true($listener->onArrayCalled);

// object
$eventManager->call('onObject', new stdClass);
$eventManager->call('onObject', new DummyString);
Assert::true($listener->onObjectCalled);

// resource
$eventManager->call('onResource', $resource = opendir(__DIR__));
closedir($resource);
Assert::true($listener->onResourceCalled);

// callable
$eventManager->call('onCallable', function () {});
$eventManager->call('onCallable', 'foo');
$eventManager->call('onCallable', [$a = new DummyString, 'a']);
Assert::true($listener->onCallableCalled);

// bool[], string[], stdClass[]
$eventManager->call('onAnnotationArray', [TRUE, FALSE, 1, 'a'], ['abc', new DummyString], [new stdClass, new stdClass]);
$eventManager->call('onAnnotationArray', [], [], []);
Assert::true($listener->onAnnotationArrayCalled);

// typehint
$eventManager->call('onTypehint', new stdClass, new DateTime);
$eventManager->call('onTypehint', new stdClass, new Nette\Utils\DateTime);
Assert::true($listener->onTypehintCalled);

// invalid annotation
Assert::exception(function () use ($eventManager) {
    $eventManager->call('onInvalidAnnotation', 1);
}, new InvalidEventArgumentTypeException);

// deep array (int[][])
$eventManager->call('onDeepArray', [[1,2,3],[4,5,6],[7,8]]);
Assert::true($listener->onDeepArrayCalled);
