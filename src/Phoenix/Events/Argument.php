<?php
/**
 * @author Tomáš Blatný
 */

namespace Phoenix\Events;

class Argument
{
    const TYPE_BOOLEAN = 'bool';
    const TYPE_NUMBER = 'number';
    const TYPE_STRING = 'string';
    const TYPE_ARRAY = 'array';
    const TYPE_OBJECT = 'object';

    public static $types = [
        self::TYPE_BOOLEAN,
        self::TYPE_NUMBER,
        self::TYPE_STRING,
        self::TYPE_ARRAY,
        self::TYPE_OBJECT,
    ];

    public function __construct($type, $value)
    {
        $this->setType($type);
        $this->setValue($value);
    }

    public function setType($type)
    {
        //
    }

    public function setValue($value)
    {
        //
    }
}
