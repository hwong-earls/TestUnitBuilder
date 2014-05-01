<?php
namespace Earls\TestBuilderBundle\Components;

use Earls\TestBuilderBundle\Components\FileHandle;
use Symfony\Component\Yaml\Yaml;

class ConfigManage
{
    private $configInfo;

    public function __construct()
    {
        $configFile = FileHandle::locate('/../Resources/config', 'config.yml');
        $this->configInfo = Yaml::parse($configFile);
    }

    public function getKey($key)
    {
        if(!isset($this->configInfo[$key])){
            return NULL;
        }
        
        return $this->configInfo[$key];
    }
}