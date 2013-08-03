<?php

/**
 * Description of BzlMailFactory
 *
 * @author Bezalel
 */
namespace BzlMail\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BzlMailFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('BzlMail\Config');
        $service = new BzlMail($config, $serviceLocator);
        
        return $service;
    }    
}
