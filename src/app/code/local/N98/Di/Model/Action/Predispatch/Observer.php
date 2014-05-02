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