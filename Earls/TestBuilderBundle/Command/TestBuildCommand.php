<?php
namespace Earls\TestBuilderBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Earls\TestBuilderBundle\Components\ClassesMetaExtract;
use Earls\TestBuilderBundle\Components\ConfigManage;
use Earls\TestBuilderBundle\Components\FileHandle;
use Earls\TestBuilderBundle\Components\PreTestGenerator;
use Earls\TestBuilderBundle\Components\TestUnitGenerator;

class TestBuildCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('testunit:builder')
            ->setDescription('Build the testunit');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {        
        $configManage = new ConfigManage();
        $fileMetaData = new PreTestGenerator(FileHandle::locate('/../../../../../..'. $configManage->getKey('startpoint')), $configManage);
        $fileEntities = $fileMetaData->getFilesMeta();
        $files = $fileEntities['files'];
        
        $countTest = 0;
        foreach($files as $entry){
            $testFile = $entry['testPath'] . '/' . $entry['testFile'];
            $testExists = FileHandle::exists($testFile);
            if(!$testExists){
                ++$countTest;
                $output->write('Try to generate: ' . substr($testFile, strrpos($testFile, $configManage->getKey('startpoint') . '/./')) , TRUE);
                try{
                    if(FileHandle::mkdir($entry['testPath'])){
                        $classMetaObj = new ClassesMetaExtract($entry);
                        $listClasses = $classMetaObj->getClasses();
                        if(!empty($listClasses)){                           
                            $testGenerator = new TestUnitGenerator($classMetaObj->getClasses(), $entry, $configManage->getKey('startpoint'));
                            $fileToCreate = $testGenerator->buildTest();
                            if (false === @file_put_contents($testFile, $fileToCreate)) {
                                throw new IOException(sprintf('Failed to write file "%s".', $filename), 0, null, $filename);
                            }
                            unset($classMetaObj);
                        }
                    }
                    else{
                        throw new \Exception('ERROR: Cannot create the folder for the testunit');
                    }
                }
                catch(\Exception $e){
                    throw new \Exception($e->getMessage());
                }
            }
        }
        $output->write($countTest . ' TestUnits Created', TRUE);
        $output->write('Process Done', TRUE);        
    }

    protected function getServicesList(OutputInterface $output)
    {
        $command = $this->getApplication()->find('container:debug');
        $input = new ArrayInput(array('container:debug'));
        $returnCode = $command->run($input, $output);
        if($returnCode != 0){
            var_dump($output);
        }
        else{
            echo "ERROR executing Container:debug";
        }
    }
}