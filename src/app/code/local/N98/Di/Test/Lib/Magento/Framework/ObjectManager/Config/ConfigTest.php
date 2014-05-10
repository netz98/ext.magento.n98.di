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
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class N98_Di_Test_Lib_Magento_Framework_ObjectManager_Config_ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testGetArgumentsEmpty()
    {
        $config = new \Magento_Framework_ObjectManager_Config_Config();
        $this->assertSame(array(), $config->getArguments('An invalid type'));
    }

    public function testExtendMergeConfiguration()
    {
        $this->_assertFooTypeArguments(new \Magento_Framework_ObjectManager_Config_Config());
    }

    /**
     * A primitive fixture for testing merging arguments
     *
     * @param Magento_Framework_ObjectManager_Config $config
     */
    private function _assertFooTypeArguments(Magento_Framework_ObjectManager_Config_Config $config)
    {
        $expected = array('argName' => 'argValue');
        $fixture = array('FooType' => array('arguments' => $expected));
        $config->extend($fixture);
        $this->assertEquals($expected, $config->getArguments('FooType'));
    }

    public function testExtendWithCacheMock()
    {
        $definitions = $this->getMockForAbstractClass('\Magento_Framework_ObjectManager_Definition');
        $definitions->expects($this->once())->method('getClasses')->will($this->returnValue(array('FooType')));

        $cache = $this->getMockForAbstractClass('\Magento_Framework_ObjectManager_ConfigCache');
        $cache->expects($this->once())->method('get')->will($this->returnValue(false));

        $config = new Magento_Framework_ObjectManager_Config_Config(null, $definitions);
        $config->setCache($cache);

        $this->_assertFooTypeArguments($config);
    }

    public function testGetPreferenceTrimsFirstSlash()
    {
        $config = new Magento_Framework_ObjectManager_Config_Config();
        $this->assertEquals('Some_Class_Name', $config->getPreference('\Some_Class_Name'));
    }

    public function testExtendIgnoresFirstSlashesOnPreferences()
    {
        $config = new Magento_Framework_ObjectManager_Config_Config();
        $config->extend(array('preferences' => array('\Some_Interface' => '\Some_Class')));
        $this->assertEquals('Some_Class', $config->getPreference('Some_Interface'));
        $this->assertEquals('Some_Class', $config->getPreference('\Some_Interface'));
    }

    public function testExtendIgnoresFirstShashesOnVirtualTypes()
    {
        $config = new Magento_Framework_ObjectManager_Config_Config();
        $config->extend(array('\SomeVirtualType' => array('type' => '\Some_Class')));
        $this->assertEquals('Some_Class', $config->getInstanceType('SomeVirtualType'));
    }

    public function testExtendIgnoresFirstShashes()
    {
        $config = new Magento_Framework_ObjectManager_Config_Config();
        $config->extend(array('\Some_Class' => array('arguments' => array('someArgument'))));
        $this->assertEquals(array('someArgument'), $config->getArguments('Some_Class'));
    }

    public function testExtendIgnoresFirstShashesForSharing()
    {
        $config = new Magento_Framework_ObjectManager_Config_Config();
        $config->extend(array('\Some_Class' => array('shared' => true)));
        $this->assertTrue($config->isShared('Some_Class'));
    }
}
