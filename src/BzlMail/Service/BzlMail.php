<?php

/**
 * Description of BzlMail
 *
 * @author Bezalel
 */

namespace BzlMail\Service;

use BzlMail\Settings;
use BzlMail\Transport;

use Zend\Config\Config;
use Zend\ServiceManager\ServiceLocatorInterface;

class BzlMail
{
    protected $config;
    
    protected $serviceLocator;
    
    protected $transportOptions;
    
    protected $storage;
    
    public function __construct(Config $config, ServiceLocatorInterface $serviceLocator = null)
    {
        $this->config = $config;
        if($serviceLocator !== null)
            $this->setServiceLocator($serviceLocator);
    }
    
    /**
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    
    /**
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
    
    /**
     * @return Transport\TransportOptions
     */
    public function getTransportOptions()
    {
        if($this->transportOptions === null){
            $this->transportOptions = new Transport\TransportOptions($this->config->transport_options, $this->getServiceLocator());
        }
        return $this->transportOptions;
    }
    
    public function getDefaultTransportOption()
    {
        $options = $this->getTransportOptions();
        if(isset($this->config->default_transport) && isset($options[$this->config->default_transport])){
            return $options[$this->config->default_transport];
        }else{
            throw new \RuntimeException('No default transport defined or registered.');
        }
    }
    
    public function getChosenTransportOption($fallbackToDefault = true)
    {
        
    }
    
    public function setStorage(Settings\Storage\Storage $storage)
    {
        $this->storage = $storage;
        return $this;
    }
    
    public function getStorage()
    {
        if($this->storage === null){
            
        }
    }
        
}
