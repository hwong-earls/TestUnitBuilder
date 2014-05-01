<?php
namespace Earls\TestBuilderBundle\Components;

use Symfony\Component\Config\FileLocator;

class FileHandle 
{
    public static function exists($filename)
    {
        return file_exists($filename);
    }

    public static function load($filename)
    {
        $buffer = array();
        if(self::exists($filename) === FALSE){
            throw new \Exception('ERROR: file ' . $filename . ' does not exists');
        }
        $fh = fopen($filename, 'r');
        if(!$fh){
            throw new \Exception('ERROR: Cannot open the file: ' . $filename);
        }
        while(!feof($fh)){
            $line = trim(substr(fgets($fh), 0, -1));
            if($line != ''){
                $buffer[] = $line;
            }
        }
        return $buffer;
    }

    public static function save($filename)
    {
        return $result;
    }

    public static function locate($path = NULL, $filename = NULL)
    {
        $path = ((empty($path)) || ($path == NULL) ? '' : $path);
        $filename = ((empty($filename)) || ($filename == NULL) ? '.' : $filename);
        $fileLocator = new FileLocator(__DIR__.$path);        
        $fileLocation = $fileLocator->locate($filename);      

        return $fileLocation;
    }
    
    public static function mkdir($path = NULL)
    {
        $subFoldersArray = array();
        $pathExists = $path;
        $subFolders = substr_count($path, '/');
        for($i=0; $i < $subFolders; $i++){
            if(file_exists($pathExists)){
                $subFoldersArray = array_reverse($subFoldersArray);
                if(empty($subFoldersArray)){
                    break;
                }
                foreach($subFoldersArray as $subFolder){
                    $pathExists .= '/' . $subFolder;
                    if(mkdir($pathExists) === FALSE){
                        throw new \Exception('ERROR: Cannot create test folder');
                    }
                }
                break;
            }
            $subFoldersArray[] = substr($pathExists, strrpos($pathExists, '/') + 1);
            $pathExists = substr($pathExists, 0, strrpos($pathExists, '/'));
        }

        return TRUE;
    }
}