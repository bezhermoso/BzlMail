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
            $this->transportOptions = new Transport\TransportOptions($this->config->transport_options->toArray(), $this->getServiceLocator());
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
    
    /**
     * 
     * @param type $fallbackToDefault
     * @return \Zend\Mail\Transport\TransportInterface
     */
    public function getChosenTransportOption($fallbackToDefault = true)
    {
        $options = $this->getTransportOptions();
        $storage = $this->getStorage();
        
        $settings = $storage->get();
        
        if($settings && isset($options[$settings->getTransport()])){
            
            /* @var $option Transport\Option\AbstractOption */
            $option = $options[$settings->getTransport()];
            $option->setSettings($settings->getSettings());
            return $option;
            
        }else if($fallbackToDefault === true){
            return $this->getDefaultTransportOption();
        }
        
    }
    
    public function setStorage(Settings\Storage\Storage $storage)
    {
        $this->storage = $storage;
        return $this;
    }
    
    /**
     * @return Settings\Storage\Storage
     */
    public function getStorage()
    {
        if($this->storage === null){
            $storage = new Settings\Storage\Storage(new Settings\Storage\Adapter\JsonConfig('data/BzlMail/settings.json'));
            $this->storage = $storage;
        }
        return $this->storage;
    }
    
    /**
     * @param \BzlMail\Settings\Settings $settings
     */
    public function saveSettings(Settings\Settings $settings)
    {
       $storage = $this->getStorage();
       $storage->save($settings);
    }
        
}
