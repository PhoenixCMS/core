<?php
/**
 * @author Tomáš Blatný
 */

namespace Phoenix\Modules;

use Phoenix\Events\EventManager;

class ModuleManager
{


    private $modules = [];

    /** @var EventManager */
    private $eventManager;


    public function __construct(EventManager $eventManager)
    {
        $this->eventManager = $eventManager;
    }


    public function addModule(Module $module)
    {
        $this->modules[$name = $module->getName() . '/' . $module->getVendor()] = $module;
        $this->eventManager->call('phoenix.modules.init', $name, $module);
        return $this;
    }


}
