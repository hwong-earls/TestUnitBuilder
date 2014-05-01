<?php
namespace Earls\TestBuilderBundle\Components;

class FoldersExplore
{
    private $root;
    private $folders;

    public function __construct($root = NULL)
    {
        $this->root = $root;
        $this->folders = array();
        
        if($root !== NULL){
            $this->setRoot($root);
        }
    }

    public function setRoot($root)
    {
        $this->validatePath($root);

        $this->root = $root;
        $this->refresh();
    }

    public function getRoot()
    {
        return $this->root;
    }

    public function getFoldersList()
    {
        return $this->folders;
    }

    public function refresh()
    {
        $this->folders = array();
        $this->folders[] = $this->root;
        $this->scan($this->root);

        return $this->folders;
    }

    public function getFolderContent($path)
    {
        $this->validatePath($path);
        $files = array();
        $dh = dir($path);
        
        while(($entry = $dh->read()) != FALSE){
            $fullEntry = $dh->path . DIRECTORY_SEPARATOR . $entry;
            if(is_file($fullEntry)){
                $files[] = $fullEntry;
            }
        }

        $dh->close(); 
   
        return $files;
    }

    public function getOnlyPHP($path)
    {
        $this->validatePath($path);

        $files = array();
        $dh = dir($path);
        
        while(($entry = $dh->read()) != FALSE){
            $fullEntry = $dh->path . DIRECTORY_SEPARATOR . $entry;
            if(is_file($fullEntry)){
                $fileContent = file_get_contents($fullEntry,FALSE,NULL,-1,5);
                if(preg_match('/^\<\?php$/i', $fileContent)){
                    $files[] = $fullEntry;
                }
            }
        }

        $dh->close(); 

        return $files;
    }

    private function validatePath($path)
    {
        if(($path == NULL) || (empty($path))){
            throw new \Exception('ERROR: Path cannot be empty');
        }
        else{
            if(!file_exists($path)){
                throw new \Exception('ERROR: Path does not exists');
            }
        }
        
        return TRUE;
    }

    private function scan($path)
    {
        $dh = dir($path);
        
        while(($entry = $dh->read()) != FALSE){
            $fullEntry = $dh->path . DIRECTORY_SEPARATOR . $entry;
            if(!is_file($fullEntry)){
                if(!preg_match('/^\.{1,2}/',$entry)){
                    $this->folders[] = $fullEntry;
                    $this->scan($fullEntry);
                }
            }
        }

        $dh->close();
    }
}