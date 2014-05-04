<?php
/**
 * netz98 magento module
 *
 * LICENSE
 *
 * This source file is subject of netz98.
 * You may be not allowed to change the sources
 * without authorization of netz98 new media GmbH.
 *
 * @copyright  Copyright (c) 1999-2014 netz98 new media GmbH (http://www.netz98.de)
 * @author netz98 new media GmbH <info@netz98.de>
 * @category N98
 * @package N98_Di
 */

class N98_Di_Model_ObjectManager_ObjectManager
{
    /**
     * @type string
     */
    const CACHE_TAG = 'N98_DI';

    /**
     * @type string
     */
    const CACHE_KEY = 'n98_di_config';

    /**
     * @type string
     */
    const CACHE_TYPE = 'n98_di';

    /**
     * @type string
     */
    const REGISTRY_KEY = 'n98_di_objectmanager';

    /**
     * @return Magento_Framework_ObjectManager_ObjectManager
     */
    public static function getInstance()
    {
        if (($objectManager = Mage::registry(self::REGISTRY_KEY)) === null) {
            $configArray = array();
            $useCache = Mage::app()->useCache(self::CACHE_TYPE);
            if ($useCache) {
                $configArray = unserialize(Mage::app()->loadCache(self::CACHE_KEY));
            }

            if (!$configArray) {
                $configReader = new Magento_Framework_ObjectManager_Config_Reader_Modules();
                $configArray = $configReader->read('global');
                if ($useCache) {
                    Mage::app()->saveCache(serialize($configArray), array(self::CACHE_TAG));
                }
            }

            /**
             * @link https://wiki.magento.com/display/MAGE2DOC/Using+Dependency+Injection#ObjectManager
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

            Mage::register(self::REGISTRY_KEY, $objectManager);
        }

        return $objectManager;
    }
}