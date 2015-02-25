<?php
/**
 * @author Tomáš Blatný
 */

namespace Phoenix\Modules;

use Nette\Neon\Neon;
use Nette\Utils\Arrays;
use Nette\Utils\Strings;
use Phoenix\IO\FileNotFoundException;

abstract class Module implements IModule
{
    /** @var array */
    private $config = NULL;

    /**
     * Returns config array
     *
     * @return array
     * @throws FileNotFoundException
     */
    public function getConfig()
    {
        if ($this->config) {
            return $this->config;
        }
        if (!file_exists($file = $this->getConfigFilePath())) {
            $this->config = [];
            throw new FileNotFoundException("Config file $file not found.");
        }
        return $this->config = $this->processConfig(Neon::decode(file_get_contents($file)));
    }


    /**
     * Returns module name
     *
     * @return string
     */
    public final function getName()
    {
        return $this->sanitizeName($this->getConfig()['name']);
    }


    /**
     * Returns module vendor
     *
     * @return string
     */
    public final function getVendor()
    {
        return $this->sanitizeName($this->getConfig()['vendor']);
    }


    /**
     * Returns path co module.neon config file
     *
     * @return string
     */
    protected function getConfigFilePath()
    {
        return __DIR__ . 'module.neon';
    }


    /**
     * Processes data from configuration file
     * @param  array $data
     * @param  bool $main
     * @return array
     * @throws FileNotFoundException
     * @throws InvalidConfigurationException
     */
    protected function processConfig(array $data, $main = TRUE)
    {
        $config = [];
        $include = [];
        if (isset($data['include'])) {
            if (is_array($data['include'])) {
                foreach ($data['include'] as $another) {
                    $include[] = $another;
                }
            } else if(is_scalar($data['include'])) {
                $include[] = $data['include'];
            }
        }
        unset($data['include']);

        if ($main) {
            foreach (['vendor', 'name', 'description', 'licence', 'version'] as $key) {
                if (isset($data[$key])) {
                    $config[$key] = (string) $data[$key];
                    unset($data[$key]);
                } else {
                    throw new InvalidConfigurationException("Missing configuration $key in configuration.");
                }
            }
        }

        foreach ($data as $key => $value) {
            $config[$key] = $value;
        }

        foreach ($include as $another) {
            if (!file_exists($another)) {
                throw new FileNotFoundException("Config file $another not found.");
            }
            $config = Arrays::mergeTree($config, $this->processConfig(Neon::decode(file_get_contents($another)), FALSE));
        }

        return $config;
    }


    private function sanitizeName($name)
    {
        return Strings::webalize($name);
    }
}
