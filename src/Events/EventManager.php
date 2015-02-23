<?php
/**
 * @author Tomáš Blatný
 */

namespace Phoenix\Events;

use Nette\DI\Container;


class EventManager
{

	/** @var Container */
	private $container;



	public function __construct(Container $container)
	{
		$this->container = $container;
		$this->container->findByType('\\Phoenix\\Events\\IEventSubscriber');
	}
}
