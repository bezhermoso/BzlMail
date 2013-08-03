<?php

/**
 * Description of ConfigFactory
 *
 * @author Bezalel
 */


namespace BzlMail\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Config\Config;

class ConfigFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        return new Config($config['bzl-mail']);
    }    
}
