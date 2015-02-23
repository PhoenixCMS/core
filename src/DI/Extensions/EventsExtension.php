<?php
/**
 * @author Tomáš Blatný
 */

namespace Phoenix\DI\Extensions;

use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\ClassType;


class EventsExtension extends CompilerExtension
{

	private $defaults = [

	];

	/**
	 * Processes configuration data. Intended to be overridden by descendant.
	 * @return void
	 */
	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		$builder->addDefinition($this->prefix('eventManager'))
			->setClass('\\Phoenix\\Events\\EventManager', $config);
	}


	/**
	 * Adjusts DI container before is compiled to PHP class. Intended to be overridden by descendant.
	 * @return void
	 */
	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();
		$subscribers = $builder;
		$builder->getDefinition($this->prefix('eventManager'))
			->addSetup('registerSubscribers', $subscribers);
	}



	/**
	 * Adjusts DI container compiled to PHP class. Intended to be overridden by descendant.
	 *
	 * @param ClassType $class
	 * @return void
	 */
	public function afterCompile(ClassType $class)
	{
	}
}
