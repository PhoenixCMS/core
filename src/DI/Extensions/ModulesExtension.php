<?php
/**
 * @author Tomáš Blatný
 */

namespace Phoenix\DI\Extensions;

use Nette\DI\CompilerExtension;

class ModulesExtension extends CompilerExtension
{
    /**
     * Processes configuration data. Intended to be overridden by descendant.
     * @return void
     */
    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $builder->addDefinition($this->prefix('moduleManager'))
            ->setClass('\\Phoenix\\Modules\\ModuleManager');
    }
}
