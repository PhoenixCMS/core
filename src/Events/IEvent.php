<?php
/**
 * @author Tomáš Blatný
 */

namespace Phoenix\Events;

interface IEvent
{
    /**
     * @return mixed
     */
    function getArguments();
    function getName();
}
