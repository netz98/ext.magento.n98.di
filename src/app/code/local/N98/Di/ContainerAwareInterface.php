<?php

interface N98_Di_ContainerAwareInterface
{
    /**
     * @param Magento_Framework_ObjectManager $objectManager
     * @return mixed
     */
    public function setContainer(Magento_Framework_ObjectManager $objectManager);
}