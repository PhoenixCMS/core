<?php
use Phoenix\Events\IEventListener;
use Tester\Assert;

/**
 * @author Tomáš Blatný
 */

class FooListener implements IEventListener {

    public $called = FALSE;


    /**
     * @eventListener onFoo
     * @param int $one
     * @param int $two
     * @param int $three
     */
    public function onFoo($one, $two, $three)
    {
        Assert::equal(1 ,$one);
        Assert::equal(2 ,$two);
        Assert::equal(3 ,$three);
        $this->called = TRUE;
    }
}
