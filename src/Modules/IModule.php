<?php
/**
 * @author Tomáš Blatný
 */

namespace Phoenix\Modules;

interface IModule
{
    /**
     * Returns module vendor
     *
     * @return string
     */
    function getVendor();

    /**
     * Returns module name
     *
     * @return string
     */
    function getName();

    /**
     * Returns module configuration
     *
     * @return array
     */
    function getConfig();
}
