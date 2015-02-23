<?php
/**
 * @author Tomáš Blatný
 */

namespace Phoenix\Events;

use Nette\Object;
use Traversable;


class Argument extends Object
{

	const TYPE_BOOLEAN = 'bool';
	const TYPE_INTEGER = 'integer';
	const TYPE_FLOAT = 'float';
	const TYPE_STRING = 'string';
	const TYPE_ARRAY = 'array';
	const TYPE_OBJECT = 'object';

	public static $types = [
		self::TYPE_BOOLEAN,
		self::TYPE_INTEGER,
		self::TYPE_FLOAT,
		self::TYPE_STRING,
		self::TYPE_ARRAY,
		self::TYPE_OBJECT,
	];


	private $name;

	private $type;

	private $value = NULL;



	public function __construct($name, $type)
	{
		$this->name = $name;
		$this->setType($type);
	}



	public function setType($type)
	{
		if (!in_array($type, self::$types)) {
			throw new InvalidEventArgumentTypeException("Argument type '$type' is not allowed.");
		}
		$this->type = $type;
		if ($this->value !== NULL) {
			$this->value = $this->fixValueType($this->value);
		}
	}



	public function setValue($value)
	{
		$this->value = $this->fixValueType($value);
	}



	/**
	 * @param  mixed $value
	 * @return mixed
	 * @throws InvalidEventArgumentTypeException
	 * @throws InvalidEventArgumentValueException
	 */
	private function fixValueType($value)
	{
		switch ($this->type) {
			case self::TYPE_BOOLEAN:
				return (bool) $value;
			case self::TYPE_INTEGER:
				if (is_numeric($value)) {
					return (int) $value;
				} else {
					throw new InvalidEventArgumentValueException("Argument '$this->name' should be integer, " . gettype($value) . ' given.');
				}
			case self::TYPE_FLOAT:
				if (is_numeric($value)) {
					return (float) $value;
				} else {
					throw new InvalidEventArgumentValueException("Argument '$this->name' should be float, " . gettype($value) . ' given.');
				}
			case self::TYPE_STRING:
				if (is_string($value)) {
					return $value;
				} elseif (is_object($value) && method_exists($value, '__toString')) {
					return (string) $value;
				} else {
					throw new InvalidEventArgumentValueException("Argument '$this->name' should be string, " . gettype($value) . ' given.');
				}
			case self::TYPE_ARRAY:
				if (is_array($value)) {
					return $value;
				} elseif (is_object($value) && $value instanceof Traversable) {
					return (array) $value;
				} else {
					throw new InvalidEventArgumentValueException("Argument '$this->name' should be string, " . gettype($value) . ' given.');
				}
			case self::TYPE_OBJECT:
				if (is_object($value)) {
					return $value;
				} elseif (is_array($value)) {
					return (object) $value;
				} else {
					throw new InvalidEventArgumentValueException("Argument '$this->name' should be string, " . gettype($value) . ' given.');
				}
			default:
				throw new InvalidEventArgumentTypeException("Unknown argument type '$this->type'.");
		}
	}
}
