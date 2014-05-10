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

class N98_Di_Model_Action_Predispatch_Observer
{
    /**
     * @param Varien_Event_Observer $observer
     */
    public function registerContainer(Varien_Event_Observer $observer)
    {
        $controller = $observer->getEvent()->getControllerAction();
        if ($controller instanceof \N98_Di_ContainerAwareInterface) {
            $controller->setContainer(N98_Di_Model_ObjectManager_ObjectManager::getInstance());
        }
    }
}