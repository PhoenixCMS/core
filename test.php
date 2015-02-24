<?php
/**
 * @author Tomáš Blatný
 */

use Nette\Reflection\ClassType;
use Phoenix\Events\EventListenerWrapper;
use Phoenix\Events\IEventListener;

require "vendor/autoload.php";

class A implements IEventListener {

    /**
     * @eventListener b
     * @param string $a
     * @param int $b
     * @param stdClass $c
     */
    public function b($a, $b, $c)
    {

    }
}

$reflection = ClassType::from($service = new A);
$method = $reflection->getMethod('b');
$wrapper = new EventListenerWrapper($service, $method);

$wrapper->call(['a', 1, new stdClass]);
