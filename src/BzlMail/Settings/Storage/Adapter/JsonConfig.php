<?php

/*
 * 
 * 
 */

/**
 * Description of JsonConfig
 *
 * @author Bezalel
 */

namespace BzlMail\Settings\Storage\Adapter;

use Zend\Config;

class JsonConfig implements AdapterInterface
{
    protected $writer;
    protected $reader;
    protected $destination;
    
    static $defaultDestination = 'data/BzlMail/settings.json';
    
    public function __construct($destination = null)
    {
        if($destination === null){
            $destination = static::$defaultDestination;
        }
        
        $this->setDestination ($destination);
    }
    
    public function setDestination($destination)
    {
        $this->destination = $destination;
    }
    
    public function getSettings()
    {
        if(!file_exists($this->destination))
            return false;
        $data = $this->getReader()->fromFile($this->destination);
        $settings = new \BzlMail\Settings\Settings($data['transport'], $data['settings']);
        return $settings;
    }

    public function hasSettings($transport)
    {
        if(!file_exists($this->destination))
            return false;
        
        $data = $this->getReader()->fromFile($this->destination);
        return $data['transport'] === $transport;
        
    }

    public function saveSettings(\BzlMail\Settings\Settings $settings)
    {
        $config = new Config\Config(array(
                        'transport' => $settings->getTransport(), 
                        'settings' => $settings->getSettings()
                    ));
        $this->getWriter()->toFile($this->destination, $config);
    }
    
    /**
     * @return Config\Writer\Json
     */
    private function getWriter()
    {
        if($this->writer === null){
            $this->writer = new Config\Writer\Json();
        }
        return $this->writer;
    }
    
    /**
     * @return Config\Reader\Json
     */
    private function getReader()
    {
        if($this->reader === null){
            $this->reader = new Config\Reader\Json();
        }
        return $this->reader;
    }
}
