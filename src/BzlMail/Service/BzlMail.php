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
            $this->transportOptions = new Transport\TransportOptions(
                    $this->config->transport_options->toArray(), 
                    $this->getServiceLocator());
        }
        return $this->transportOptions;
    }
    
    /**
     * 
     * @param type $fallbackToDefault
     * @return Transport\Option\AbstractOption
     */
    public function getChosenOption()
    {
        $options = $this->getTransportOptions();
        $storage = $this->getStorage();
        
        $settings = $storage->get();
        
        if ($settings && isset($options[$settings->getTransport()])) {
            
            /* @var $option Transport\Option\AbstractOption */
            $option = $options[$settings->getTransport()];
            $option->setSettings($settings->getSettings());
            return $option;
            
        } else {
            throw new \Exception('No transport option set yet.');
        }
    }
    
    public function setChosenOption(Transport\Option\AbstractOption $option)
    {
        $transportKey = null;
        
        foreach ($this->getTransportOptions() as $key => $transportOption) {
            if (get_class($option) === get_class($transportOption)) {
                $transportKey = $key;
                break;
            }
        }
        if ($transportKey === null) {
           throw new \RuntimeException(
                   sprintf(
                           "Cannot save option %s. It is not found within BzlMail\Transport\TransportOptions", 
                           get_class($option)
                    ));
        } else {
            
            $settings = new Settings\Settings($transportKey, $option->getSettings());
            $this->saveSettings($settings);
            
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
        if ($this->storage === null) {
            $storage = new Settings\Storage\Storage();
            
            $adapterName = $this->config->settings_storage_adapter;
            
            if ($adapterName instanceof Settings\Storage\Adapter\AdapterInterface) {
                $storageAdapter = $adapterName;
            } elseif (is_string($adapterName) && $this->getServiceLocator()->has($adapterName)){
                $storageAdapter = $this->getServiceLocator()->get($adapterName);
            } elseif (is_string($adapterName) && class_exists($adapterName)) {
                $storageAdapter = new $adapterName();
            } else {
                throw new \RuntimeException('Cannot instantiate the appropriate setting storage adapter.');
            }
            
            $storage->setAdapter($storageAdapter);
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
    
    public function getSettings()
    {
        return $this->getStorage()->get();
    }
}
