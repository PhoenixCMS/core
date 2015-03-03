<?php
/**
 * @author Tomáš Blatný
 */

namespace Phoenix\Events;

use Nette\Reflection\Method;
use Traversable;

/**
 * Wrapper for event listener.
 */
class EventListenerWrapper
{
    /** @var IEventListener */
    private $service;

    /** @var Method */
    private $method;

    /** @var array */
    private $annotations;

    /**
     * @param  IEventListener $service
     * @param  Method $method
     */
    public function __construct($service, Method $method)
    {
        $this->service = $service;
        $this->method = $method;
        $this->annotations = $method->getAnnotations();
    }

    /**
     * Calls wrapped event
     *
     * @param  array $arguments
     * @throws InvalidEventArgumentDefinitionException
     */
    public function call(array $arguments)
    {
        $parameters = $this->method->getParameters();
        if (!isset($this->annotations['param'])) {
            throw new InvalidEventArgumentDefinitionException('@param annotation at method ' . $this->method->getName() . ' not found.');
        }
        $count = count($parameters);
        for ($i = 0; $i < $count; $i++) {
            $arguments[$i] = $this->validateArgument($arguments[$i], $i);
        }
        call_user_func_array([$this->service, $this->method->getName()], $arguments);
    }

    /**
     * Validates, if argument has its annotation and converts it to defined type.
     *
     * @param  mixed $argument
     * @param  int $i
     * @return mixed
     * @throws InvalidEventArgumentDefinitionException
     * @throws InvalidEventArgumentTypeException
     * @throws InvalidEventArgumentValueException
     */
    private function validateArgument($argument, $i)
    {
        $annotations = $this->annotations;
        if (!isset($annotations['param'][$i])) {
            throw new InvalidEventArgumentDefinitionException('Not all parameters have their own @param annotation at method ' . $this->method->getName() . '.');
        }
        $annotation = $annotations['param'][$i];
        return $this->convertToType($argument, $annotation);
    }

    /**
     * Converts $argument to type defined in $annotation
     *
     * @param  mixed $argument
     * @param  string $annotation
     * @return mixed
     * @throws InvalidEventArgumentTypeException
     * @throws InvalidEventArgumentValueException
     */
    private function convertToType($argument, $annotation)
    {
        list($type, $name) = explode(' ', $annotation, 3);
        switch ($type) {
            case 'null':
                if (is_null($argument)) {
                    return $argument;
                } else {
                    throw new InvalidEventArgumentValueException("Argument '$name' should be null, " . gettype($argument) . ' given.');
                }
            case 'bool':
            case 'boolean':
                return (bool) $argument;
            case 'int':
            case 'integer':
            case 'number':
                if (is_numeric($argument)) {
                    return (int) $argument;
                } else {
                    throw new InvalidEventArgumentValueException("Argument '$name' should be integer, " . gettype($argument) . ' given.');
                }
            case 'float':
            case 'double':
                if (is_numeric($argument)) {
                    return (float) $argument;
                } else {
                    throw new InvalidEventArgumentValueException("Argument '$name' should be float, " . gettype($argument) . ' given.');
                }
            case 'string':
                if (is_scalar($argument)) {
                    return (string) $argument;
                } elseif (is_object($argument) && method_exists($argument, '__toString')) {
                    return (string) $argument;
                } else {
                    throw new InvalidEventArgumentValueException("Argument '$name' should be string, " . gettype($argument) . ' given.');
                }
            case 'scalar':
                if (is_scalar($argument)) {
                    return $argument;
                } else {
                    throw new InvalidEventArgumentValueException("Argument '$name' should be scalar, " . gettype($argument) . ' given.');
                }
            case 'array':
                if (is_array($argument)) {
                    return $argument;
                } elseif (is_object($argument) && $argument instanceof Traversable) {
                    return (array) $argument;
                } else {
                    throw new InvalidEventArgumentValueException("Argument '$name' should be array, " . gettype($argument) . ' given.');
                }
            case 'object':
                if (is_object($argument)) {
                    return $argument;
                } elseif (is_array($argument)) {
                    return (object) $argument;
                } else {
                    throw new InvalidEventArgumentValueException("Argument '$name' should be object, " . gettype($argument) . ' given.');
                }
            case 'mixed':
                return $argument;
            case 'resource':
                if (is_resource($argument)) {
                    return $argument;
                } else {
                    throw new InvalidEventArgumentValueException("Argument '$name' should be resource, " . gettype($argument) . ' given.');
                }
            case 'callable':
            case 'callback':
                if (is_callable($argument)) {
                    return $argument;
                } else {
                    throw new InvalidEventArgumentValueException("Argument '$name' should be callable, " . gettype($argument) . ' given.');
                }
            default:
                // string[], int[], stdClass[], etc
                if (substr($type, -2) === '[]') {
                    $argument = $this->convertToType($argument, 'array ' . $name);
                    foreach ($argument as $key => $value) {
                        $argument[$key] = $this->convertToType($value, substr($type, 0, -2) . ' ' . $name);
                    }
                    return $argument;
                }
                if (class_exists($type) || interface_exists($type) || trait_exists($type)) {
                    if (!$argument instanceof $type) {
                        throw new InvalidEventArgumentValueException("Argument '$name' should be $type or its descendant, " . get_class($argument) . ' given.');
                    }
                    return $argument;
                }

                throw new InvalidEventArgumentTypeException("Unknown argument type '$type'.");
        }
    }
}
