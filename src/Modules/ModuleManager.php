<?php
/**
 * @author Tomáš Blatný
 */

namespace Phoenix\Modules;

use Phoenix\Events\EventManager;

class ModuleManager
{

    /** @var IModule[] */
    private $modules = [];

    /** @var EventManager */
    private $eventManager;


    public function __construct(EventManager $eventManager)
    {
        $this->eventManager = $eventManager;
    }

    /**
     * @param IModule $module
     * @return $this
     */
    public function addModule(IModule $module)
    {
        $this->modules[$name = $module->getName() . '/' . $module->getVendor()] = $module;
        $this->eventManager->call('phoenix.modules.init', $name, $module);
        return $this;
    }

    /**
     * @return IModule[]
     */
    public function getModules()
    {
        return $this->modules;
    }


}
