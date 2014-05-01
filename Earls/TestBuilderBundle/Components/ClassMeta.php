<?php
namespace Earls\TestBuilderBundle\Components;

class ClassMeta
{
    private $filename;
    private $className;
    private $namespace;
    private $methods;
    private $testPath;
    private $testFilename;

    public function __construct()
    {
        $this->className = NULL;
        $this->namespace = NULL;
        $this->methods = array();
        $this->testPath = NULL;
        $this->testFilename = NULL;
    }

    public function getFilename()
    {
        return $this->filename;
    }
    
    public function getName()
    {
        return $this->className;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function getMethods()
    {
        return $this->methods;
    }

    public function setFilename($name)
    {
        $this->filename = $name;
        
        return $this;
    }

    public function setName($name)
    {
        $this->className = $name;
        
        return $this;
    }

    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
        
        return $this;
    }

    public function setMethods(array $methods)
    {
        $this->methods = $methods;

        return $this;
    }

    public function setTestPath($path)
    {
        $this->testPath = $path;
        
        return $this;
    }
    
    public function getTestPath()
    {
        return $this->testPath;
    }

    public function setTestFilename($filename)
    {
        $this->testFilename = $filename;
        
        return $this;
    }

    public function getTestFilename()
    {
        return $this->testFilename;
    }
}