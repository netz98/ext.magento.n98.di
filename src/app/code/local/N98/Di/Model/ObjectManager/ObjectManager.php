<?php
/**
 * Magento Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    N98
 * @package     N98_Di
 * @copyright   Copyright (c) 2014 netz98 new media GmbH. (http://www.netz98.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
     * @return Magento_Framework_ObjectManager
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
            $diConfig = new Magento_Framework_ObjectManager_Config_Config();
            $diConfig->extend($configArray);
            $factory = new Magento_Framework_ObjectManager_Factory_Factory($diConfig);

            $sharedInstances = array(
                'Magento_Framework_ObjectManager_Config'     => $diConfig,
            );

            $objectManager = new Magento_Framework_ObjectManager_ObjectManager($factory, $diConfig, $sharedInstances);
            $factory->setObjectManager($objectManager);

            Mage::register(self::REGISTRY_KEY, $objectManager);
        }

        return $objectManager;
    }
}