<?php

require __DIR__ . '/files/ChildInterface.php';
require __DIR__ . '/files/DiParent.php';
require __DIR__ . '/files/Child.php';
require __DIR__ . '/files/Child/A.php';
require __DIR__ . '/files/Child/Circular.php';
require __DIR__ . '/files/Aggregate/AggregateInterface.php';
require __DIR__ . '/files/Aggregate/AggregateParent.php';
require __DIR__ . '/files/Aggregate/Child.php';
require __DIR__ . '/files/Aggregate/WithOptional.php';

class N98_Di_Test_Lib_Magento_Framework_ObjectManager_ObjectManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Framework_ObjectManager_ObjectManager
     */
    protected $_object;

    protected function setUp()
    {
        $config = new \Magento_Framework_ObjectManager_Config_Config(
            new \Magento_Framework_ObjectManager_Relations_Runtime()
        );
        $factory = new \Magento_Framework_ObjectManager_Factory_Factory($config, null, null, array(
            'first_param' => 'first_param_value',
            'second_param' => 'second_param_value'
        ));
        $this->_object = new \Magento_Framework_ObjectManager_ObjectManager($factory, $config);
        $factory->setObjectManager($this->_object);
    }

    public function testCreateCreatesNewInstanceEveryTime()
    {
        $objectA = $this->_object->create('Magento_Test_Di_Child');
        $this->assertInstanceOf('Magento_Test_Di_Child', $objectA);
        $objectB = $this->_object->create('Magento_Test_Di_Child');
        $this->assertInstanceOf('Magento_Test_Di_Child', $objectB);
        $this->assertNotSame($objectA, $objectB);
    }

    public function testGetCreatesNewInstanceOnlyOnce()
    {
        $objectA = $this->_object->get('Magento_Test_Di_Child');
        $this->assertInstanceOf('Magento_Test_Di_Child', $objectA);
        $objectB = $this->_object->get('Magento_Test_Di_Child');
        $this->assertInstanceOf('Magento_Test_Di_Child', $objectB);
        $this->assertSame($objectA, $objectB);
    }

    public function testCreateCreatesPreferredImplementation()
    {
        $this->_object->configure(
            array(
                'preferences' => array(
                    'Magento_Test_Di_DiInterface' => 'Magento_Test_Di_DiParent',
                    'Magento_Test_Di_DiParent' => 'Magento_Test_Di_Child'
                )
            )
        );
        $interface = $this->_object->create('Magento_Test_Di_DiInterface');
        $parent = $this->_object->create('Magento_Test_Di_DiParent');
        $child = $this->_object->create('Magento_Test_Di_Child');
        $this->assertInstanceOf('Magento_Test_Di_Child', $interface);
        $this->assertInstanceOf('Magento_Test_Di_Child', $parent);
        $this->assertInstanceOf('Magento_Test_Di_Child', $child);
        $this->assertNotSame($interface, $parent);
        $this->assertNotSame($interface, $child);
    }

    public function testGetCreatesPreferredImplementation()
    {
        $this->_object->configure(
            array(
                'preferences' => array(
                    'Magento_Test_Di_DiInterface' => 'Magento_Test_Di_DiParent',
                    'Magento_Test_Di_DiParent' => 'Magento_Test_Di_Child'
                )
            )
        );
        $interface = $this->_object->get('Magento_Test_Di_DiInterface');
        $parent = $this->_object->get('Magento_Test_Di_DiParent');
        $child = $this->_object->get('Magento_Test_Di_Child');
        $this->assertInstanceOf('Magento_Test_Di_Child', $interface);
        $this->assertInstanceOf('Magento_Test_Di_Child', $parent);
        $this->assertInstanceOf('Magento_Test_Di_Child', $child);
        $this->assertSame($interface, $parent);
        $this->assertSame($interface, $child);
    }

    /**
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage Missing required argument $scalar of Magento_Test_Di_Aggregate_AggregateParent
     */
    public function testCreateThrowsExceptionIfRequiredConstructorParameterIsNotProvided()
    {
        $this->_object->configure(
            array(
                'preferences' => array(
                    'Magento_Test_Di_DiInterface' => 'Magento_Test_Di_DiParent',
                    'Magento_Test_Di_DiParent' => 'Magento_Test_Di_Child'
                )
            )
        );
        $this->_object->create('Magento_Test_Di_Aggregate_AggregateParent');
    }

    public function testCreateResolvesScalarParametersAutomatically()
    {
        $this->_object->configure(
            array(
                'preferences' => array(
                    'Magento_Test_Di_DiInterface' => 'Magento_Test_Di_DiParent',
                    'Magento_Test_Di_DiParent' => 'Magento_Test_Di_Child'
                ),
                'Magento_Test_Di_Aggregate_AggregateParent' => array(
                    'arguments' => array(
                        'child' => array('instance' => 'Magento_Test_Di_Child_A'),
                        'scalar' => 'scalarValue'
                    )
                )
            )
        );
        /** @var $result \Magento_Test_Di_Aggregate_AggregateParent */
        $result = $this->_object->create('Magento_Test_Di_Aggregate_AggregateParent');
        $this->assertInstanceOf('Magento_Test_Di_Aggregate_AggregateParent', $result);
        $this->assertInstanceOf('Magento_Test_Di_Child', $result->interface);
        $this->assertInstanceOf('Magento_Test_Di_Child', $result->parent);
        $this->assertInstanceOf('Magento_Test_Di_Child_A', $result->child);
        $this->assertEquals('scalarValue', $result->scalar);
        $this->assertEquals('1', $result->optionalScalar);
    }

    public function testGetCreatesSharedInstancesEveryTime()
    {
        $this->_object->configure(
            array(
                'preferences' => array(
                    'Magento_Test_Di_DiInterface' => 'Magento_Test_Di_DiParent',
                    'Magento_Test_Di_DiParent' => 'Magento_Test_Di_Child'
                ),
                'Magento_Test_Di_DiInterface' => array('shared' => 0),
                'Magento_Test_Di_Aggregate_AggregateParent' => array(
                    'arguments' => array('scalar' => 'scalarValue')
                )
            )
        );
        /** @var $result \Magento_Test_Di_Aggregate_AggregateParent */
        $result = $this->_object->create('Magento_Test_Di_Aggregate_AggregateParent');
        $this->assertInstanceOf('Magento_Test_Di_Aggregate_AggregateParent', $result);
        $this->assertInstanceOf('Magento_Test_Di_Child', $result->interface);
        $this->assertInstanceOf('Magento_Test_Di_Child', $result->parent);
        $this->assertInstanceOf('Magento_Test_Di_Child', $result->child);
        $this->assertNotSame($result->interface, $result->parent);
        $this->assertNotSame($result->interface, $result->child);
        $this->assertSame($result->parent, $result->child);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Circular dependency: Magento_Test_Di_Aggregate_AggregateParent depends on
     * Magento_Test_Di_Child\Circular and vice versa.
     */
    public function testGetDetectsCircularDependency()
    {
        $this->_object->configure(
            array(
                'preferences' => array(
                    'Magento_Test_Di_DiInterface' => 'Magento_Test_Di_DiParent',
                    'Magento_Test_Di_DiParent'    => 'Magento_Test_Di_Child_Circular'
                )
            )
        );
        $this->_object->create('Magento_Test_Di_Aggregate_AggregateParent');
    }

    public function testCreateIgnoresOptionalArguments()
    {
        $instance = $this->_object->create('Magento_Test_Di_Aggregate_WithOptional');
        $this->assertNull($instance->parent);
        $this->assertNull($instance->child);
    }

    public function testCreateCreatesPreconfiguredInstance()
    {
        $this->_object->configure(
            array(
                'preferences' => array(
                    'Magento_Test_Di_DiInterface' => 'Magento_Test_Di_DiParent',
                    'Magento_Test_Di_DiParent' => 'Magento_Test_Di_Child'
                ),
                'customChildType' => array(
                    'type' => 'Magento_Test_Di_Aggregate_Child',
                    'arguments' => array(
                        'scalar' => 'configuredScalar',
                        'secondScalar' => 'configuredSecondScalar',
                        'secondOptionalScalar' => 'configuredOptionalScalar'
                    )
                )
            )
        );
        $customChild = $this->_object->get('customChildType');
        $this->assertInstanceOf('Magento_Test_Di_Aggregate_Child', $customChild);
        $this->assertEquals('configuredScalar', $customChild->scalar);
        $this->assertEquals('configuredSecondScalar', $customChild->secondScalar);
        $this->assertEquals(1, $customChild->optionalScalar);
        $this->assertEquals('configuredOptionalScalar', $customChild->secondOptionalScalar);
        $this->assertSame($customChild, $this->_object->get('customChildType'));
    }

    public function testParameterShareabilityConfigurationIsApplied()
    {
        $this->_object->configure(
            array(
                'customChildType' => array(
                    'type' => 'Magento_Test_Di_Aggregate_Child',
                    'arguments' => array(
                        'interface' => array('instance' => 'Magento_Test_Di_DiParent'),
                        'scalar' => 'configuredScalar',
                        'secondScalar' => 'configuredSecondScalar'
                    )
                )
            )
        );
        $childA = $this->_object->create('customChildType');
        $childB = $this->_object->create('customChildType');
        $this->assertNotSame($childA, $childB);
        $this->assertSame($childA->interface, $childB->interface);

        $this->_object->configure(
            array(
                'customChildType' => array(
                    'arguments' => array(
                        'interface' => array(
                            'instance' => 'Magento_Test_Di_DiParent',
                            'shared' => false
                        )
                    )
                )
            )
        );
        $childA = $this->_object->create('customChildType');
        $childB = $this->_object->create('customChildType');
        $this->assertNotSame($childA, $childB);
        $this->assertNotSame($childA->interface, $childB->interface);
    }

    public function testTypeShareabilityConfigurationIsApplied()
    {
        $this->_object->configure(
            array(
                'customChildType' => array(
                    'type' => 'Magento_Test_Di_Aggregate_Child',
                    'arguments' => array(
                        'interface' => array('instance' => 'Magento_Test_Di_DiParent'),
                        'scalar' => 'configuredScalar',
                        'secondScalar' => 'configuredSecondScalar'
                    )
                )
            )
        );
        $childA = $this->_object->create('customChildType');
        $childB = $this->_object->create('customChildType');
        $this->assertNotSame($childA, $childB);
        $this->assertSame($childA->interface, $childB->interface);

        $this->_object->configure(array('Magento_Test_Di_DiParent' => array('shared' => false)));

        $parent1 = $this->_object->create('Magento_Test_Di_DiParent');
        $parent2 = $this->_object->create('Magento_Test_Di_DiParent');
        $this->assertNotSame($parent1, $parent2);

        $childA = $this->_object->create('customChildType');
        $childB = $this->_object->create('customChildType');
        $this->assertNotSame($childA, $childB);
    }

    public function testParameterShareabilityConfigurationOverridesTypeShareability()
    {
        $this->_object->configure(
            array(
                'Magento_Test_Di_DiParent' => array('shared' => false),
                'customChildType' => array(
                    'type' => 'Magento_Test_Di_Aggregate_Child',
                    'arguments' => array(
                        'interface' => array('instance' => 'Magento_Test_Di_DiParent'),
                        'scalar' => 'configuredScalar',
                        'secondScalar' => 'configuredSecondScalar'
                    )
                )
            )
        );
        $childA = $this->_object->create('customChildType');
        $childB = $this->_object->create('customChildType');
        $this->assertNotSame($childA, $childB);
        $this->assertNotSame($childA->interface, $childB->interface);

        $this->_object->configure(
            array(
                'customChildType' => array(
                    'arguments' => array(
                        'interface' => array(
                            'instance' => 'Magento_Test_Di_DiParent',
                            'shared' => true
                        )
                    )
                )
            )
        );
        $childA = $this->_object->create('customChildType');
        $childB = $this->_object->create('customChildType');
        $this->assertNotSame($childA, $childB);
        $this->assertSame($childA->interface, $childB->interface);
    }

    public function testGlobalArgumentsCanBeConfigured()
    {
        $this->_object->configure(
            array(
                'preferences' => array('Magento_Test_Di_DiInterface' => 'Magento_Test_Di_DiParent'),
                'Magento_Test_Di_Aggregate_AggregateParent' => array(
                    'arguments' => array(
                        'scalar' => array('argument' => 'first_param'),
                        'optionalScalar' => array('argument' => 'second_param')
                    )
                )
            )
        );
        /** @var $result \Magento_Test_Di_Aggregate_AggregateParent */
        $result = $this->_object->create('Magento_Test_Di_Aggregate_AggregateParent');
        $this->assertEquals('first_param_value', $result->scalar);
        $this->assertEquals('second_param_value', $result->optionalScalar);
    }

    public function testConfiguredArgumentsAreInherited()
    {
        $this->_object->configure(
            array(
                'Magento_Test_Di_Aggregate_AggregateParent' => array(
                    'arguments' => array(
                        'interface' => array('instance' => 'Magento_Test_Di_DiParent'),
                        'scalar' => array('argument' => 'first_param'),
                        'optionalScalar' => 'parentOptionalScalar'
                    )
                ),
                'Magento_Test_Di_Aggregate_Child' => array(
                    'arguments' => array(
                        'secondScalar' => 'childSecondScalar'
                    )
                )
            )
        );

        /** @var $result \Magento_Test_Di_Aggregate_AggregateParent */
        $result = $this->_object->create('Magento_Test_Di_Aggregate_Child');
        $this->assertInstanceOf('Magento_Test_Di_DiParent', $result->interface);
        $this->assertEquals('first_param_value', $result->scalar);
        $this->assertEquals('childSecondScalar', $result->secondScalar);
        $this->assertEquals('parentOptionalScalar', $result->optionalScalar);
    }

    public function testConfiguredArgumentsOverrideInheritedArguments()
    {
        $this->_object->configure(
            array(
                'Magento_Test_Di_Aggregate_AggregateParent' => array(
                    'arguments' => array(
                        'interface' => array('instance' => 'Magento_Test_Di_DiParent'),
                        'scalar' => array('argument' => 'first_param'),
                        'optionalScalar' => 'parentOptionalScalar'
                    )
                ),
                'Magento_Test_Di_Aggregate_Child' => array(
                    'arguments' => array(
                        'interface' => array('instance' => 'Magento_Test_Di_Child'),
                        'scalar' => array('argument' => 'second_param'),
                        'secondScalar' => 'childSecondScalar',
                        'optionalScalar' => 'childOptionalScalar'
                    )
                )
            )
        );

        /** @var $result \Magento_Test_Di_Aggregate_AggregateParent */
        $result = $this->_object->create('Magento_Test_Di_Aggregate_Child');
        $this->assertInstanceOf('Magento_Test_Di_Child', $result->interface);
        $this->assertEquals('second_param_value', $result->scalar);
        $this->assertEquals('childSecondScalar', $result->secondScalar);
        $this->assertEquals('childOptionalScalar', $result->optionalScalar);
    }

    public function testGetIgnoresFirstSlash()
    {
        $this->assertSame($this->_object->get('Magento_Test_Di_Child'), $this->_object->get('\Magento_Test_Di_Child'));
    }
}
