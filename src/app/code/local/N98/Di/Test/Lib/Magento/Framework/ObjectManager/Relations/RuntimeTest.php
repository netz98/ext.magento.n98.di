<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 * 
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require_once __DIR__ . '/../files/Child.php';

class N98_Di_Test_Lib_Magento_Framework_ObjectManager_Relations_RuntimeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento_Framework_ObjectManager_Relations_Runtime
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new \Magento_Framework_ObjectManager_Relations_Runtime();
    }

    /**
     * @param $type
     * @param $parents
     * @dataProvider getParentsDataProvider
     */
    public function testGetParents($type, $parents)
    {
        $this->assertEquals($parents, $this->_model->getParents($type));
    }

    public function getParentsDataProvider()
    {
        return array(
            array('Magento_Test_Di_DiInterface', array()),
            array('Magento_Test_Di_DiParent', array(null, 'Magento_Test_Di_DiInterface')),
            array('Magento_Test_Di_Child', array('Magento_Test_Di_DiParent', 'Magento_Test_Di_ChildInterface'))
        );
    }
}
