<?php

namespace BzlMail\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager;
use Zend\Mail;
use Zend\Mime;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer;

/**
 * Description of BzlSend
 *
 * @author BHermoso
 */
class BzlSend extends AbstractPlugin implements ServiceManager\ServiceLocatorAwareInterface
{
    public function __invoke()
    {
        return $this->getCompositionFacade();
    }
    
    public function getCompositionFacade()
    {
        return $this->getServiceLocator()->get('bzlmail.composer');
    }
    
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function setServiceLocator(ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator->getServiceLocator();
    }
}
