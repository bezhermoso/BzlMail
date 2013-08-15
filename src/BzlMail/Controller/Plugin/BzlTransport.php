<?php

namespace BzlMail\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use BzlMail\Service;
use Zend\ServiceManager;

/**
 * Description of BzlTransport
 *
 * @author Bezalel
 */
class BzlTransport extends AbstractPlugin implements ServiceManager\ServiceLocatorAwareInterface
{
    protected $service;
    
    protected $serviceLocator;
    
    public function __invoke()
    {
        $option = $this->getService()->getChosenOption();
        return $option->getTransport();
    }
    
    /**
     * @return Service\BzlMail
     */
    public function getService()
    {
        if ($this->service === null) {
            
            $this->service = $this->getServiceLocator()
                                  ->getServiceLocator()
                                  ->get('BzlMail\Service\BzlMail');
        }
        
        return $this->service;
    }
    
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
    
    public function setServiceLocator(ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
}
