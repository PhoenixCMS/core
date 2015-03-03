<?php
/**
 * @author Tomáš Blatný
 */

namespace Phoenix\Database;


use LeanMapper\Repository as LMRepository;
use Phoenix\Modules\IModule;

abstract class Repository extends LMRepository
{
    const CORE_TABLE_PREFIX = 'core';

    /** @var IModule */
    private $module = NULL;

    public function setModule(IModule $module)
    {
        $this->module = $module;
        $this->mapper->registerRepository($this);
    }

    public function getModule()
    {
        return $this->module;
    }

    public function getTable()
    {
        if (!$this->table) {
            $original = $this->mapper->getTableByRepositoryClass(get_called_class());
            if (!$this->module) {
                $this->table = self::CORE_TABLE_PREFIX . '__' . $original;
            } else {
                $this->table = $this->module->getVendor() . '_' . $this->module->getName() . '__' . $original;
            }
        }
        return $this->table;
    }
}
