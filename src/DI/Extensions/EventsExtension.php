<?php
/**
 * @author Tomáš Blatný
 */

namespace Phoenix\DI\Extensions;

use Nette\DI\CompilerExtension;


class EventsExtension extends CompilerExtension
{

	/**
	 * Processes configuration data. Intended to be overridden by descendant.
	 * @return void
	 */
	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$builder->addDefinition($this->prefix('eventManager'))
			->setClass('\\Phoenix\\Events\\EventManager');
	}

}
