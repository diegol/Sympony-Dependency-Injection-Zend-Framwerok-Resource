<?php
/*
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * This is the Application Resource for sfServiceContainer
 * 
 * More information for the sympony container:
 *  http://components.symfony-project.org/dependency-injection/
 *
 * @author   Diego Lewin  <diego.lewin@gmail.com>
 */
class Custom_Resource_Symfonycontainer extends 
                                    Zend_Application_Resource_ResourceAbstract 
{

    /**
     * Library path not defined
     */
    const ERROR_LIBRARY_PATH_NOT_DEFINED = 'Library path not defined.';
    
    /**
     * No dump file defined in the configuration for the php container
     */
    const ERROR_DUMP_FILE_NOT_DEFINED = 'No Dump file Defined';
        
    /**
     * No loader available for the extension
     */
    const ERROR_NO_LOADER_AVAILABLE =  "No loader available for extension '%s'";
    
    /**
     * Container class not defined
     */
    const ERROR_CONTAINER_CLASS_NOT_DEFINED = 'Container Class not defned.';

    /**
     * Generate Container class not defined
     */    
    const ERROR_GENERATE_CONTAINER_CLASS_NOT_DEFINED = 'Generate Container Class not defned.';
    
    /**
     * Container file not defined
     */
    const ERROR_CONTAINER_FILE_NOT_DEFINED = 'Container File not defned';
    
    
    /**
     * Instance of  sfServiceContainerBuilder
     *          
     * @var sfServiceContainerBuilder
     */
    protected $_sfServiceContainerBuilder = null;
    
    
    /**
     * Returns an instance of sfServiceContainerBuilder  
     * 
     * The options in application.ini are:
     * 
     * resources.symfonycontainer.libraryPath                  = LIBRARY_PATH  "/Symfony/DependencyInjection/"
     * resources.symfonycontainer.configContainerFile          = APPLICATION_PATH "/configs/services/sfServices-development.xml"
     * resources.symfonycontainer.dumpFile                     = APPLICATION_PATH "/Containers/SymfonyContainerDev.php"
     * resources.symfonycontainer.containerClass               = "SymfonyContainerDev"
     * resources.symfonycontainer.generateContainerClasses     = true
     *
     * @return Custom_Resource_Symfonycontainer     
     */
    public function init() 
    {
        //check if already exist
        if (null === $this->_sfServiceContainerBuilder) {
           
            $this->_checkOptions();
           
           $this->_sfServiceContainerBuilder = $this->_getContainerBuilder();
        }

        return $this->_sfServiceContainerBuilder;
    }
    
    /**
     * Return sf container builder
     * 
     * @return sfServiceContainerBuilder 
     */
    private function _getContainerBuilder() {
        
        require_once $this->_options['libraryPath'] . 'sfServiceContainerAutoloader.php';

        sfServiceContainerAutoloader::register();        
        $dumpContainerFile = $this->_options['dumpFile'];

        $containerClass = $this->_options['containerClass'];         
       
        //if we generate class we don't use old file
        if (
            $this->_options['generateContainerClasses'] == 0 && 
            !file_exists($dumpContainerFile)) {

            require_once $dumpContainerFile;
            return new $containerClass;            
        }
        
        $containerBuilder = new sfServiceContainerBuilder($this->_options);

        $this->_loadContainerConfiguration(
                $this->_options['configContainerFile'],
                $containerBuilder
                );
    
        $this->_dumpContainerToPhp(
                $containerBuilder ,
                $dumpContainerFile ,
                $containerClass
                );        

        return $containerBuilder;
    }
    
    
    /**
     * Check options from application.ini
     * if not set throw an exception
     * 
     * @return void
     */
     private function _checkOptions()
     {
         
        if(!isset($this->_options['libraryPath'])) {
            throw new Exception(self::ERROR_LIBRARY_PATH_NOT_DEFINED);
        }

        if(!isset($this->_options['dumpFile'])) {
            throw new Exception(self::ERROR_DUMP_FILE_NOT_DEFINED);
        }

        if(!isset($this->_options['containerClass'])) {
            throw new Exception(self::ERROR_CONTAINER_CLASS_NOT_DEFINED);
        }
        
        if(!isset($this->_options['generateContainerClasses'])) {
            throw new Exception(self::ERROR_GENERATE_CONTAINER_CLASS_NOT_DEFINED);
        }
        
        if(!isset($this->_options['configContainerFile'])) {
            throw new Exception(self::ERROR_CONTAINER_FILE_NOT_DEFINED);
        }
    }
    
    /**
     * Build the container from the config file
     * 
     * @param string $configContainerFile
     * @param object $containerBuilder 
     * 
     * @return void
     */
    private function _loadContainerConfiguration($configContainerFile, &$containerBuilder) {
         
       /**
        * Create a builder to which we attach a loader so
        * we can load the configuration file.
        */
  
        $fileExtension = pathinfo($configFile, PATHINFO_EXTENSION);

        switch($fileExtension) {
            case 'xml':
                $containerLoader = new sfServiceContainerLoaderFileXml($containerBuilder);
                break;
            case 'init':
                $containerLoader = new sfServiceContainerLoaderFileIni($containerBuilder);
                break;
            case 'yaml':
                $containerLoader = new sfServiceContainerLoaderFileYaml($containerBuilder);
                break;
            default:
                throw new Exception(
                    sprintf(ERROR_NO_LOADER_AVAILABLE, $fileExtension)
                    );
                break;
        }
        $containerLoader->load($configFile);
        
        
    }

    /**
     * Dump the cotainer to a php class file
     * 
     * @param object $containerLoader
     * @param string $dumpContainerFile name of the container file
     * @param string $containerClass  name of the container class
     * @return void
     */
    private function _dumpContainerToPhp($containerLoader, $dumpContainerFile, $containerClass) {

        $dumper = new sfServiceContainerDumperPhp($containerLoader);

        file_put_contents(
            $dumpContainerFile,
            $dumper->dump(
                array('class' => $containerClass)
            )
        );           
        
    }
    
    
}
