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
    
    public function __call($name, $arguments)
    {
        $facade = $this->getCompositionFacade();
        
        if (method_exists($facade, $name)) {
            if (preg_match('/^get/', $name)) {
                return call_user_func_array(array($facade, $name), $arguments);
            } else {
                call_user_func_array(array($facade, $name), $arguments);
                return $this;
            }
        }
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
