<?php
/**
 * @author Tomáš Blatný
 */

namespace Phoenix\Events;

use Nette\DI\Container;
use Nette\Reflection\ClassType;


class EventManager
{
	const ANNOTATION_EVENT_LISTENER = 'eventListener';

	/** @var Container */
	private $container;

	/** @var EventListenerWrapper[][] */
	private $eventListeners = [];


	public function __construct(Container $container)
	{
		$this->container = $container;
		$subscribers = $this->container->findByType('\\Phoenix\\Events\\IEventListener');
		foreach ($subscribers as $class) {
			$reflection = ClassType::from($container->getService($class));
			foreach ($reflection->getMethods() as $method) {
				if ($method->hasAnnotation(self::ANNOTATION_EVENT_LISTENER)) {
					$event = (string) $method->getAnnotation(self::ANNOTATION_EVENT_LISTENER);
					if (!isset($this->eventListeners[$event])) {
						$this->eventListeners[$event] = [];
					}
					$this->eventListeners[$event][] = new EventListenerWrapper($container->getService($class), $method);
				}
			}
		}
	}


	public function call($event/*, $args...*/)
	{
		if (isset($this->eventListeners[$event])) {
			foreach ($this->eventListeners[$event] as $eventListener) {
				$eventListener->call(array_slice(func_get_args(), 1));
			}
		}
	}
}
