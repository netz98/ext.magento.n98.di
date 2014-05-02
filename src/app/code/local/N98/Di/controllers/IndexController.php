<?php

class N98_Di_IndexController
    extends Mage_Core_Controller_Front_Action
    implements N98_Di_ContainerAwareInterface
{
    /**
     * @var Magento_Framework_ObjectManager
     */
    protected $_container;

    public function indexAction()
    {
        var_dump('test');
        var_dump($this->_container->get('Mage_Core_Helper_Data'));
    }

    /**
     * @param Magento_Framework_ObjectManager $objectManager
     * @return mixed
     */
    public function setContainer(Magento_Framework_ObjectManager $objectManager)
    {
        $this->_container = $objectManager;
    }
}