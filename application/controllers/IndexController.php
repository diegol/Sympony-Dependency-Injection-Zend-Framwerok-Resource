<?php

class IndexController extends Zend_Controller_Action
{

    /**
     * Instance of symfonycontainer
     * @var object
     */
    private $_symfonycontainer;
    
    public function init()
    {
         $this->_symfonycontainer = $this->getFrontController()->getParam('bootstrap')
                 ->getPluginResource('symfonycontainer');
    }

    public function indexAction()
    {
       $this->_symfonycontainer->getSymfonycontainer()->getService('logger')->info("This is a test");
       
       //var_dump($this->_symfonycontainer->getSymfonycontainer());//->getService('logger'); //->info("This is a test");
    }


}

