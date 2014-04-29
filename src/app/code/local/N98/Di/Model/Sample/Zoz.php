<?php

class N98_Di_Model_Sample_Zoz
{
    protected $a;
    protected $b;
    protected $product;

    /**
     * @param stdClass $a
     */
    public function __construct(stdClass $a, Mage_Catalog_Model_Product $product, $b = 'test')
    {
        $this->a = $a;
        $this->b = $b;
        $this->product = $product;
    }
}