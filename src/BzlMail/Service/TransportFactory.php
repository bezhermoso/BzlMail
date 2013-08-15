<?php

/*
 * 
 * 
 */

namespace BzlMail\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Description of TransportFactory
 *
 * @author Bezalel
 */
class TransportFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $services)
    {
        $service = $services->get('BzlMail\Service\BzlMail');
        /* @var $service BzlMail */
        $option = $service->getChosenOption();
        
        return $option->getTransport();
    }

}
