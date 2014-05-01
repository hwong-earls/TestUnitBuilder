<?php
namespace Earls\TestBuilderBundle\Components;

class TestUnitGenerator
{
    public static $OPEN_BRAKET = '{';
    public static $CLOSE_BRAKET = '}';

    private $outputBuffer;
    private $classMeta;
    private $fileMeta;

    public function __construct(array $classMeta = NULL, array $fileMeta = NULL)
    {
        $this->classMeta = $classMeta;
        $this->fileMeta = $fileMeta;
        $this->outputBuffer = ''; //---- Initialize Output Buffer, set empty
        
        if($classMeta !== NULL){
            //$this->buildTest();
        }
    }

    public function setFilename($classMeta)
    {
        $this->classMeta = $classMeta;
        
        return $this->classMeta;
    }

    public function getFilename()
    {
        return $this->classMeta;
    }

    public function buildTest()
    {
        $this->buildMainStruct();

        return $this->outputBuffer;
    }

    private function buildMainStruct()
    {
        $this->outputBuffer .= $this->writeln("<?php"); 
        $this->outputBuffer .= $this->writeln("namespace " . $this->getNamespace() . ";\n");
        $this->outputBuffer .= $this->addUse();
        $this->outputBuffer .= $this->writeln("class " . $this->getClassName() . "Test extends \PHPUnit_Framework_TestCase");
        $this->outputBuffer .= $this->writeln(self::$OPEN_BRAKET);
        $this->outputBuffer .= $this->writeln("\tprivate " . $this->getClassName() . ";\n");
        $this->outputBuffer .= $this->addSetUp();
        $this->outputBuffer .= $this->addFunctionTest();
        $this->outputBuffer .= $this->addTearDown();
        $this->outputBuffer .= $this->writeln(self::$CLOSE_BRAKET);
    }

    private function getNamespace()
    {
        $namespace = $this->fileMeta['testPath'];

        return $namespace;
    }

    private function getClassName()
    {
        $explodeClass = explode('\\', $this->classMeta[0]->getName());
        $className = array_pop($explodeClass);

        return $className;
    }
    
    private function addUse()
    {
        $output = '';
        $output .= 'use ' . $this->fileMeta['file'] . ";\n";

        return $this->writeln($output);
    }

    private function addFunctionTest()
    {
        $output = '';
        $methods = $this->classMeta[0]->getMethods();
        foreach($methods as $method){
            $methodCall = ucfirst($method);
            $output .= $this->writeln("\tpublic function test{$methodCall}()");
            $output .= $this->writeln("\t". self::$OPEN_BRAKET);
            $output .= $this->writeln("\t\t" . '$result = $this->' . $this->getClassName() . '->' . $method . '();');
            $output .= $this->writeln("\t\t" . '$this->assertEquals(0,0);');
            $output .= $this->writeln("\t" . self::$CLOSE_BRAKET);
            $output .= $this->writeln();
        }
        
        return $output;
    }

    private function addSetUp()
    {
        $output = '';
        $output .= $this->writeln("\tprotected function setUp()");
        $output .= $this->writeln("\t". self::$OPEN_BRAKET);
        $output .= $this->writeln("\t\t" . '$this->' . $this->getClassName() . " = new " . $this->getClassName() . "();");        
        $output .= $this->writeln("\t\t// Your code here");        
        $output .= $this->writeln("\t" . self::$CLOSE_BRAKET);
        $output .= $this->writeln();

        return $output;
    }

    private function addTearDown()
    {
        $privateObject = $this->getClassName();
        $output = '';
        $output .= $this->writeln("\tprotected function tearDown()");
        $output .= $this->writeln("\t". self::$OPEN_BRAKET);
        $output .= $this->writeln("\t\t" . 'unset($this->' . $privateObject . ');');        
        $output .= $this->writeln("\t\t// Your code here");        
        $output .= $this->writeln("\t" . self::$CLOSE_BRAKET);
        $output .= $this->writeln();

        return $output;
    }

    private function writeln($string = '')
    {
        return sprintf("%s\n", $string);
    }
}