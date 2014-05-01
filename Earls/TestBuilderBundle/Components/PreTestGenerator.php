<?php
namespace Earls\TestBuilderBundle\Components;

use Earls\TestBuilderBundle\Components\FoldersExplore;
use Earls\TestBuilderBundle\Components\ConfigManage;
use Earls\TestBuilderBundle\Components\FileHandle;

class PreTestGenerator
{
    private $configManage;
    private $startpoint;
    private $fileMeta;

    public function __construct($startpoint, ConfigManage $configManage)
    {
        $this->startpoint = $startpoint;
        $this->configManage = $configManage;
        $this->fileMeta = array();

        $this->build();
    }

    public function getFilesMeta()
    {
        return $this->fileMeta;
    }

    protected function build()
    {
        $folder = new FoldersExplore($this->startpoint);
        $folders =  $folder->getFoldersList();

        $files = $this->scanFile($folders);
        $this->fileMeta = $this->createTestStructure($files);
    }

    protected function scanFile(array $folders)
    {
        $CONST_BUNDLE = '/Bundle.php$/';

        $bundlePath = '';
        $files = array();
        $excludeDirPattern = $this->getExcludeDirPattern();
        $excludeFilePattern = $this->getExcludeFilePattern();

        $folder = new FoldersExplore();
        foreach($folders as $folderEntry){
            if(!preg_match($excludeDirPattern, $folderEntry)){
                foreach($folder->getOnlyPHP($folderEntry) as $file){
                    if(!preg_match($excludeFilePattern, $file)){                        
                        $files[] = array('file' => $file, 'bundlePath' => $bundlePath, 'testPath' => ($bundlePath != '' ? $bundlePath . '/Tests' : './Tests'));
                    }
                    if(preg_match($CONST_BUNDLE, $file)){
                        $bundlePath = substr($file, 0, strrpos($file, '/'));
                    }
                }
            }
        }

        return $files;
    }

    protected function createTestStructure(array $files)
    {
        $filesTest = array();

        foreach($files as $entry){
            $file = $entry['file'];
            $path = substr($file, 0, strrpos($file, '/'));
            $testSubFolder = $entry['testPath'] . str_replace($entry['bundlePath'], '', $path);
            $testFile = str_replace('.php', '', substr(str_replace($path, '', $file), 1)) . 'Test.php';
            $filesTest[] = array('file' => $file, 'testPath' => $testSubFolder, 'testFile' => $testFile);
        }

        return array('files' => $filesTest, 'bundlePath' => $path, 'testPath' => $testSubFolder . $testFile);
    }

    protected function getExcludeDirPattern()
    {
        $excludeDir = $this->configManage->getKey('excludeDir');
        $pattern = '';
        foreach($excludeDir as $entry){
            $pattern .= ($pattern == '' ? '' : '|') . $entry;
        }

        return ($pattern == '' ? '' : sprintf("/%s/", $pattern));
    }

    protected function getExcludeFilePattern()
    {
        $excludeFile = $this->configManage->getKey('excludeFile');
        $pattern = '';
        foreach($excludeFile as $entry){
            $pattern .= ($pattern == '' ? '' : '|') . $entry;
        }

        return ($pattern == '' ? '' : sprintf("/%s$/", $pattern));        
    }
}

    