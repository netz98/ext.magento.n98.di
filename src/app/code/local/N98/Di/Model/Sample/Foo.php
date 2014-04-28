<?php

class N98_Di_Model_Sample_Foo
{
    /**
     * @var N98_Di_Model_Sample_BarInterface
     */
    public $_bar;

    public function __construct(N98_Di_Model_Sample_BarInterface $bar)
    {
        $this->bar = $bar;
    }
}