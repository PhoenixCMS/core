<?php
/**
 * @author Tomáš Blatný
 */

namespace Phoenix\DI\Extensions;

use Nette\DI\CompilerExtension;

class DatabaseExtension extends CompilerExtension
{

    /**
     * Processes configuration data. Intended to be overridden by descendant.
     * @return void
     */
    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $builder->addDefinition($this->prefix('mapper'))
            ->setClass('\\Phoenix\\Database\\Mapper');
    }

    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();
        $extensions = $this->compiler->getExtensions('\\Phoenix\\DI\\Extensions\\ModulesExtension');
        if (count($extensions)) {
            $builder->getDefinition($this->prefix('mapper'))
                ->addSetup('setModuleManager');
        }
    }

}
