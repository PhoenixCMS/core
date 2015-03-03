<?php
use Phoenix\Events\IEventListener;
use Tester\Assert;

/**
 * @author Tomáš Blatný
 */

class VariableTypesListener implements IEventListener
{

    public $onNullCalled = FALSE;
    public $onBoolCalled = FALSE;
    public $onIntCalled = FALSE;
    public $onFloatCalled = FALSE;
    public $onStringCalled = FALSE;
    public $onScalarCalled = FALSE;
    public $onArrayCalled = FALSE;
    public $onObjectCalled = FALSE;
    public $onResourceCalled = FALSE;
    public $onCallableCalled = FALSE;
    public $onAnnotationArrayCalled = FALSE;
    public $onTypehintCalled = FALSE;
    public $onDeepArrayCalled = FALSE;

    /**
     * @eventListener onNull
     * @param  null $null
     */
    public function onNull($null)
    {
        Assert::type('null', $null);
        $this->onNullCalled = TRUE;
    }

    /**
     * @eventListener onBool
     * @param  bool $bool
     * @param  boolean $boolean
     */
    public function onBool($bool, $boolean)
    {
        Assert::type('bool', $bool);
        Assert::type('bool', $boolean);
        $this->onBoolCalled = TRUE;
    }

    /**
     * @eventListener onInt
     * @param  int $int
     * @param  integer $integer
     * @param  number $number
     */
    public function onInt($int, $integer, $number)
    {
        Assert::type('int', $int);
        Assert::type('int', $integer);
        Assert::type('int', $number);
        $this->onIntCalled = TRUE;
    }

    /**
     * @eventListener onFloat
     * @param  float $float
     * @param  double $double
     */
    public function onFloat($float, $double)
    {
        Assert::type('float', $float);
        Assert::type('float', $double);
        $this->onFloatCalled = TRUE;
    }

    /**
     * @eventListener onString
     * @param  string $string
     */
    public function onString($string)
    {
        Assert::type('string', $string);
        $this->onStringCalled = TRUE;
    }

    /** @noinspection PhpUndefinedClassInspection (for IDE) */
    /**
     * @eventListener onScalar
     * @param  scalar $scalar
     */
    public function onScalar($scalar)
    {
        Assert::type('scalar', $scalar);
        $this->onScalarCalled = TRUE;
    }

    /**
     * @eventListener onArray
     * @param  array $array
     */
    public function onArray($array)
    {
        Assert::type('array', $array);
        $this->onArrayCalled = TRUE;
    }

    /**
     * @eventListener onObject
     * @param  object $object
     */
    public function onObject($object)
    {
        Assert::type('object', $object);
        $this->onObjectCalled = TRUE;
    }

    /**
     * @eventListener onResource
     * @param  resource $resource
     */
    public function onResource($resource)
    {
        Assert::type('resource', $resource);
        $this->onResourceCalled = TRUE;
    }

    /**
     * @eventListener onCallable
     * @param  callable $callable
     */
    public function onCallable($callable)
    {
        Assert::type('callable', $callable);
        $this->onCallableCalled = TRUE;
    }

    /**
     * @eventListener onAnnotationArray
     * @param  bool[] $boolArray
     * @param  string[] $stringArray
     * @param  stdClass[] $stdClassArray
     */
    public function onAnnotationArray($boolArray, $stringArray, $stdClassArray)
    {
        Assert::type('array', $boolArray);
        Assert::type('array', $stringArray);
        Assert::type('array', $stdClassArray);

        foreach ($boolArray as $bool) {
            Assert::type('bool', $bool);
        }

        foreach ($stringArray as $string) {
            Assert::type('string', $string);
        }

        foreach ($stdClassArray as $stdClass) {
            Assert::type('stdClass', $stdClass);
        }
        $this->onAnnotationArrayCalled = TRUE;
    }

    /**
     * @eventListener onTypehint
     * @param  stdClass $stdClass
     * @param  DateTime $dateTime
     */
    public function onTypehint($stdClass, $dateTime)
    {
        Assert::type('stdClass', $stdClass);
        Assert::type('DateTime', $dateTime);
        $this->onTypehintCalled = TRUE;
    }

    /** @noinspection PhpUndefinedClassInspection (for IDE) */
    /**
     * @eventListener onInvalidAnnotation
     * @param  NonExistingClass $invalid
     * @throws \Tester\AssertException
     */
    public function onInvalidAnnotation($invalid)
    {
        unset($invalid);
        Assert::fail('Invalid annotation passed.');
    }

    /**
     * @eventListener onDeepArray
     * @param  int[][] $array
     */
    public function onDeepArray($array)
    {
        Assert::type('array', $array);

        foreach ($array as $innerArray) {
            Assert::type('array', $innerArray);
            foreach ($innerArray as $int) {
                Assert::type('int', $int);
            }
        }
        $this->onDeepArrayCalled = TRUE;
    }
}
