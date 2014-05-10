Magento Module N98_Di
=====================

Magento 2 Dependency Injection Backport for Magento 1

The modules contains parts of the Magento 2 "lib/Magento/Framework" folder.
All namespaced class names are ported to old style PEAR names which enables Varien_Autoload to
load the classes.

Getting started
---------------

Install N98_Di module in your Magento store.
Place a new **di.xml** in your module config (etc) folder.

Now you are able to use the ported object manager.


Example config:

``` xml
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="../../../../../../lib/Magento/Framework/ObjectManager/etc/config.xsd">
    <!-- Replace class with another -->
    <preference for="N98_Di_Model_Sample_Foo" type="N98_Di_Model_Sample_Bar" />

    <!-- Good old rewrite -->
    <preference for="Mage_Core_Helper_Data" type="Mage_Core_Helper_String" />

    <!-- Implement interface -->
    <preference for="N98_Di_Model_Sample_BarInterface" type="N98_Di_Model_Sample_Bar" />

    <virtualType name="myVirtualType" type="N98_Di_Model_Sample_Foo" />
    <virtualType name="myProduct" type="Mage_Catalog_Model_Product" />

    <type name="N98_Di_Model_Sample_Bar">
        <arguments>
            <argument name="value" xsi:type="string">Hallo Welt</argument>
        </arguments>
    </type>

    <type name="N98_Di_Model_Sample_Zoz">
        <arguments>
            <argument name="a" xsi:type="object">myVirtualType</argument>
            <argument name="product" xsi:type="object">myProduct</argument>
        </arguments>
    </type>
</config>
```

A refenrence of the di.xml file can be found here: 

[https://wiki.magento.com/display/MAGE2DOC/Using+Dependency+Injection#ObjectManager](https://wiki.magento.com/display/MAGE2DOC/Using+Dependency+Injection#ObjectManager)


Usage in a Controller
---------------------

The module is delivered with an observer which is called in preDispatch event of each controller.
The observer injects the ObjectManager if the controller implements the interface **N98_Di_ContainerAwareInterface**.


Example:

``` php
<?php

class Acme_Foo_IndexController extends Mage_Core_Controller_Front_Action implements N98_Di_ContainerAwareInterface
{
    /**
     * @var Magento_Framework_ObjectManager
     */
    protected $_container;

    public function indexAction()
    {
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
```

Usage over helper
-----------------

``` php
<?php

$objectManager = Mage::helper('n98_di')->getObjectManager();
$bar = $objectManager->create('Acme_Foo_Model_Bar');
```   
   
   
Known problems
--------------

The module is very experimental.
Currently not all parts of the dependency injection framework are ported.
This includes features like the definition compiler or the interceptors (plugins).


