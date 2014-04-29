<?php

require_once 'app/Mage.php';

Mage::app();

$configReader = new Magento_Framework_ObjectManager_Config_Reader_Modules();
$configArray = $configReader->read('global');

/**
 * @link https://wiki.magento.com/display/MAGE2DOC/Using+Dependency+Injection#ObjectManager
 */

/*$configArray = array(
    'preferences' => array(
        'N98_Di_Model_Sample_Foo'          => 'N98_Di_Model_Sample_Bar',
        'Mage_Core_Helper_Data'            => 'Mage_Core_Helper_String',
        'N98_Di_Model_Sample_BarInterface' => 'N98_Di_Model_Sample_Bar',
    ),
    'N98_Di_Model_Sample_Bar' => array(
        'arguments' => array(
            'value' => 'This is the value of Bar'
        )
    ),
    'myVirtualType' => array(
        'type' => 'N98_Di_Model_Sample_Foo',
    ),
    'N98_Di_Model_Sample_Zoz' => array(
        'arguments' => array(
            'product' => array('instance' => 'Mage_Catalog_Model_Product'),
            'a' => array('instance' => 'myVirtualType'),
        )
    )
);
*/

$classReader = new Magento_Framework_Code_Reader_ClassReader();
$relations = new Magento_Framework_ObjectManager_Relations_Runtime($classReader);
$definitions = new Magento_Framework_ObjectManager_Definition_Runtime($classReader);

$booleanUtils = new Magento_Framework_Stdlib_BooleanUtils();
$diConfig = new Magento_Framework_ObjectManager_Config_Config($relations, $definitions);
$diConfig->extend($configArray);
$factory = new Magento_Framework_ObjectManager_Factory($diConfig, null, $definitions);

$sharedInstances = array(
    'Magento\Framework\ObjectManager\Relations'  => $relations,
    'Magento_Framework_ObjectManager_Config'     => $diConfig,
    'Magento_Framework_ObjectManager_Definition' => $definitions,
    'Magento_Framework_Stdlib_BooleanUtils'      => $booleanUtils,
);

$objectManager = new Magento_Framework_ObjectManager_ObjectManager($factory, $diConfig, $sharedInstances);
$factory->setObjectManager($objectManager);

//var_dump($objectManager->get('Mage_Core_Helper_Data'));
//var_dump($objectManager->get('N98_Di_Model_Sample_Zoz'));
//var_dump($objectManager->create('Mage_Catalog_Model_Product'));

var_dump($objectManager->create('N98_Di_Model_Sample_Zoz', array('b' => 'Nicht test')));