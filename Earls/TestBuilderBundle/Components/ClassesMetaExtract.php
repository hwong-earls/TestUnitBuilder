<?php
namespace Earls\TestBuilderBundle\Components;

use Earls\TestBuilderBundle\Components\FileHandle;
use Earls\TestBuilderBundle\Components\ClassMeta;

class ClassesMetaExtract
{
    private $fileEntity;
    private $classes; 
    private $fileContent;
    private $filename;

    public function __construct(array $fileEntity = NULL)
    {
        $this->fileEntity = $fileEntity;
        $this->setFilename($this->fileEntity['file']);
    }

    public function setFilename($filename)
    {
        if($filename !== NULL){
            if(FileHandle::exists($filename)){
                $this->init($filename);
            }
            else{
                throw new \Exception('ERROR: File does not exists');
            }
        }
        else{
            throw new \Exception('ERROR: Filename cannot be empty');
        }
        
        return $this;
    }

    public function getFilename($filename)
    {
        return $this->filename;
    }
     
    public function setTestPath($path)
    {
    }

    public function getFileContent()
    {
        return $this->fileContent;
    }

    public function getClasses()
    {
        return $this->classes;
    }

    private function init($filename)
    {        
        $this->filename = $filename;
        $this->classes = array();
        $this->fileContent = FileHandle::load($filename);
        if($this->isPHPScript() === FALSE){
            throw new \Exception('ERROR: File is not PHP Script');
        }
        $listClasses = $this->extractClasses();
        if(!empty($listClasses)){
            $this->extractClassesInfo($listClasses);
        }
    }

    private function isPHPScript()
    {
        $firstLine = $this->fileContent[0];
        $regex = preg_match('/^\<\?php$/i', $firstLine);

        return  ($regex == 0 ? FALSE : $regex);
    }

    private function extractClasses()
    {
        $classes = array();
        $namespace = '';
        $tempLine = '';
        foreach($this->fileContent as $line){
            if($tempLine == ''){
                if(preg_match('/^namespace.*;/i',$line)){
                    $namespace = trim(substr($line, 9));
                    $namespace = substr($namespace, 0, strrpos($namespace, ';'));
                }
                if($this->classIn($line) !== FALSE){
                    $tempLine .= $line;
                }
            }
            else{
                $tempLine .= ' ' . $line;
            }
            if(($tempLine !== '') && ($this->openBraketIn($line) !== FALSE)){                    
                $className = $this->getClassName($this->trimArray(explode(' ', $tempLine)));
                $classes[] = array('namespace' => $namespace, 'classname' => $className);
                $tempLine = '';
            }
        }

        return $classes;
    }

    private function extractClassesInfo(array $listClasses)
    {
        foreach($listClasses as $class){
            try{
            $classname = '\\' . $class['namespace'] . '\\' . $class['classname'];
            if(class_exists($classname) === FALSE){
                @require $this->filename;
            }
            $classReflection = new \ReflectionClass($classname);
            $methods = $classReflection->getMethods();
            $methodsTest = array();
            foreach($methods as $methodReflection){
                if(($methodReflection->isPublic()) && (!$methodReflection->isConstructor())){
                    $methodsTest[] = $methodReflection->name; 
                }
            }

            $classMetaObj = new ClassMeta();
            $classMetaObj
                ->setFilename($this->filename)
                ->setName($classReflection->getName())
                ->setNamespace($classReflection->getNamespaceName())
                ->setMethods($methodsTest);

            $this->classes[] = $classMetaObj;
            }
            catch(\Exception $e){
                $this->classes[] = new ClassMeta();
            }
        }
    }


    private function classIn($line)
    {
        $regex = preg_match('/^\bclass\b/i', $line);

        return ($regex == 0 ? FALSE : $regex);
    }

    private function openBraketIn($line)
    {
        $regex = preg_match('/\{/', $line);

        return ($regex == 0 ? FALSE : $regex);
    }    
    
    private function trimArray(array $array)
    {
        $resultArray = array();
        foreach($array as $element){
            if(!empty($element)){
                $resultArray[] = $element;
            }
        }
        
        return $resultArray;
    }

    private function getClassName(array $array)
    {
        $className = NULL;
        foreach($array as $element){
            if($className !== NULL){
                $className = $element;
                break;
            }
            if(preg_match('/class/i', $element) !== FALSE){
                $className = '';
            }
        }

        return $className;
    }
}