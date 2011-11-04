<?php

class SymfonyContainer extends sfServiceContainer
{
  protected $shared = array();

  public function __construct()
  {
    parent::__construct($this->getDefaultParameters());
  }

  protected function get_a7a78509dab06a2d2833d380b1bca1732Service()
  {
    if (isset($this->shared['_a7a78509dab06a2d2833d380b1bca173_2'])) return $this->shared['_a7a78509dab06a2d2833d380b1bca173_2'];

    $instance = new Zend_Log_Writer_Stream($this->getParameter('logger.file'));

    return $this->shared['_a7a78509dab06a2d2833d380b1bca173_2'] = $instance;
  }

  protected function get_a7a78509dab06a2d2833d380b1bca1731Service()
  {
    if (isset($this->shared['_a7a78509dab06a2d2833d380b1bca173_1'])) return $this->shared['_a7a78509dab06a2d2833d380b1bca173_1'];

    $instance = new Zend_Log_Writer_Stream('php://output');

    return $this->shared['_a7a78509dab06a2d2833d380b1bca173_1'] = $instance;
  }

  protected function getLoggerService()
  {
    if (isset($this->shared['logger'])) return $this->shared['logger'];

    $instance = new Zend_Log();
    $instance->addWriter($this->getService('_a7a78509dab06a2d2833d380b1bca173_1'));
    $instance->addWriter($this->getService('_a7a78509dab06a2d2833d380b1bca173_2'));

    return $this->shared['logger'] = $instance;
  }

  protected function getDefaultParameters()
  {
    return array(
      'librarypath' => 'Symfony/DependencyInjection/',
      'configcontainerfile' => '/home/diegolewin/di/Sympony-Dependency-Injection-Zend-Framwerok-Resource/application/configs/services/sfServices.xml',
      'dumpfile' => '/home/diegolewin/di/Sympony-Dependency-Injection-Zend-Framwerok-Resource/application/containers/SymfonyContainer.php',
      'containerclass' => 'SymfonyContainer',
      'generatecontainerclasses' => '',
      'logger.file' => '/tmp/log-production',
    );
  }
}
