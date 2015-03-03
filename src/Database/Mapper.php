<?php
/**
 * @author Tomáš Blatný
 */

namespace Phoenix\Database;

use LeanMapper\DefaultMapper;
use LeanMapper\Exception\InvalidStateException;
use LeanMapper\Row;
use Phoenix\Modules\IModule;
use Phoenix\Modules\ModuleManager;

class Mapper extends DefaultMapper
{

    /** @var ModuleManager */
    private $moduleManager;

    /** @var array FQN repository class => FQN entity class */
    private $repositoryToEntityMapping = [];

    /** @var Repository[] table name => FQN repository class */
    private $tableToRepositoryMapping = [];

    /**
     * @param ModuleManager $moduleManager
     */
    public function setModuleManager(ModuleManager $moduleManager)
    {
        $this->moduleManager = $moduleManager;
        foreach ($moduleManager->getModules() as $module) {
            $this->addModuleMapping($module);
        }
    }

    /**
     * @param Repository $repository
     */
    public function registerRepository(Repository $repository)
    {
        $this->tableToRepositoryMapping[$repository->getTable()] = $repository;
    }

    /**
     * @param $repository
     * @param $entity
     * @throws InvalidMappingException
     */
    public function addEntityMapping($repository, $entity)
    {
        $repository = ltrim($repository, '\\');
        $entity = ltrim($entity, '\\');
        if (isset($this->repositoryToEntityMapping[$repository])) {
            throw new InvalidMappingException("Repository $repository already has mapping assigned.");
        }
        if (in_array($entity, $this->repositoryToEntityMapping)) {
            throw new InvalidMappingException("Entity $entity already has mapping assigned.");
        }
        if (class_exists($repository) && class_exists($entity)) {
            $this->repositoryToEntityMapping[$this->getTableByRepositoryClass($repository)] = $entity;
        } else {
            throw new InvalidMappingException("Invalid mapping, repository $repository and/or entity $entity does not exist.");
        }
    }

    /**
     * Gets table name from given fully qualified entity class name
     *
     * @param  string $entityClass
     * @return string
     */
    public function getTable($entityClass)
    {
        $entityClass = ltrim($entityClass, '\\');
        if (($repository = array_search($entityClass, $this->repositoryToEntityMapping)) !== FALSE) {
            $repositoryClass = ltrim(get_class($repository), '\\');
            if (($table = array_search($repositoryClass, $this->tableToRepositoryMapping)) !== FALSE) {
                return $table;
            }
        }
        return parent::getTable($entityClass);
    }

    /**
     * Gets fully qualified entity class name from given table name
     *
     * @param  string $table
     * @param  Row|null $row
     * @return string
     */
    public function getEntityClass($table, Row $row = NULL)
    {
        if (isset($this->tableToRepositoryMapping[$table])) {
            $repository = get_class($this->tableToRepositoryMapping[$table]);
            if (isset($this->repositoryToEntityMapping[$repository])) {
                return $this->repositoryToEntityMapping[$repository];
            }
        }
        return parent::getEntityClass($table, $row);
    }

    /**
     * Gets table column name from given fully qualified entity class name and entity field name
     *
     * @param  string $entityClass
     * @param  string $field
     * @return string
     */
    public function getColumn($entityClass, $field)
    {
        return $this->toUnderScore($field);
    }

    /**
     * Gets entity field (property) name from given table name and table column
     *
     * @param  string $table
     * @param  string $column
     * @return string
     */
    public function getEntityField($table, $column)
    {
        return $this->toCamelCase($column);
    }

    /**
     * Gets table name from repository class name
     *
     * @param  string $repositoryClass
     * @return string
     * @throws InvalidStateException
     */
    public function getTableByRepositoryClass($repositoryClass)
    {
        $matches = array();
        if (preg_match('#([a-z0-9]+)repository$#i', $repositoryClass, $matches)) {
            return $this->toUnderScore($matches[1]);
        }
        throw new InvalidStateException('Cannot determine table name.');
    }

    /**
     * @param  string $str
     * @return string
     */
    protected function toUnderScore($str)
    {
        return lcfirst(preg_replace_callback('#(?<=.)([A-Z])#', function ($m) {
            return '_' . strtolower($m[1]);
        }, $str));
    }

    /**
     * @param  string $str
     * @return string
     */
    protected function toCamelCase($str)
    {
        return preg_replace_callback('#_(.)#', function ($m) {
            return strtoupper($m[1]);
        }, $str);
    }

    /**
     * @param  IModule $module
     * @throws InvalidMappingException
     */
    protected function addModuleMapping(IModule $module)
    {
        $config = $module->getConfig();
        if (isset($config['database']) && isset($config['database']['mapping'])) {
            $mapping = $config['database']['mapping'];
            if (is_array($mapping)) {
                foreach ($mapping as $key => $value) {
                    $this->addEntityMapping($key, $value);
                }
            }
        }
    }
}
