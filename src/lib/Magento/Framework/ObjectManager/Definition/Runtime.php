<?php
/**
 * Runtime class definitions. \Reflection is used to parse constructor signatures. Should be used only in dev mode.
 *
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

class Magento_Framework_ObjectManager_Definition_Runtime
    implements Magento_Framework_ObjectManager_Definition
{
    /**
     * @var array
     */
    protected $_definitions = array();

    /**
     * @var Magento_Framework_Code_Reader_ClassReader
     */
    protected $_reader = null;

    /**
     * @param Magento_Framework_Code_Reader_ClassReader $reader
     */
    public function __construct(Magento_Framework_Code_Reader_ClassReader $reader = null)
    {
        if ($reader == null) {
            $reader = new Magento_Framework_Code_Reader_ClassReader();
        }
        $this->_reader = $reader;
    }

    /**
     * Get list of method parameters
     *
     * Retrieve an ordered list of constructor parameters.
     * Each value is an array with following entries:
     *
     * array(
     *     0, // string: Parameter name
     *     1, // string|null: Parameter type
     *     2, // bool: whether this param is required
     *     3, // mixed: default value
     * );
     *
     * @param string $className
     * @return array|null
     */
    public function getParameters($className)
    {
        if (!array_key_exists($className, $this->_definitions)) {
            $this->_definitions[$className] = $this->_reader->getConstructor($className);
        }
        return $this->_definitions[$className];
    }

    /**
     * Retrieve list of all classes covered with definitions
     *
     * @return array
     */
    public function getClasses()
    {
        return array();
    }
}
