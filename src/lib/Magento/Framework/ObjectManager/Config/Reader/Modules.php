<?php

class Magento_Framework_ObjectManager_Config_Reader_Modules implements Magento_Framework_Config_ReaderInterface
{
    /**
     * @var array
     */
    protected $_idAttributes = array(
        '/config/preference' => 'for',
        '/config/(type|virtualType)' => 'name',
        '/config/(type|virtualType)/plugin' => 'name',
        '/config/(type|virtualType)/arguments/argument' => 'name',
        '/config/(type|virtualType)/arguments/argument(/item)+' => 'name'
    );

    /**
     * Read configuration scope
     *
     * @param string|null $scope
     * @return array
     */
    public function read($scope = null)
    {
        $schemaFile = null;
        $config = new Magento_Framework_Config_Dom(
            '<config />',
            $this->_idAttributes,
            'xsi:type',
            $schemaFile
        );

        //$configXml = Mage::getConfig()->loadModulesConfiguration('di.xml', null, $mergerModel);
        //$domDocument = new DOMDocument();
        //$domDocument->loadXML();

        $config->merge(file_get_contents(Mage::getModuleDir('etc', 'N98_Di') . '/di.xml'));

        //$converter = new Magento_Framework_Config_Converter_Dom();
        //$data = $converter->convert();

        $booleanUtils = new Magento_Framework_Stdlib_BooleanUtils();
        $constInterpreter = new Magento_Framework_Data_Argument_Interpreter_Constant();
        $result = new Magento_Framework_Data_Argument_Interpreter_Composite(
            array(
                'boolean' => new Magento_Framework_Data_Argument_Interpreter_Boolean($booleanUtils),
                'string' => new Magento_Framework_Data_Argument_Interpreter_String($booleanUtils),
                'number' => new Magento_Framework_Data_Argument_Interpreter_Number(),
                'null' => new Magento_Framework_Data_Argument_Interpreter_NullType(),
                'object' => new Magento_Framework_Data_Argument_Interpreter_Object($booleanUtils),
                'const' => $constInterpreter,
                //'init_parameter' => new Magento_Framework_App_Arguments_ArgumentInterpreter($constInterpreter)
            ),
            'xsi:type'
        );
        // Add interpreters that reference the composite
        $result->addInterpreter('array', new Magento_Framework_Data_Argument_Interpreter_ArrayType($result));

        $mapper = new Magento_Framework_ObjectManager_Config_Mapper_Dom($result);

        return $mapper->convert($config->getDom());
    }
}