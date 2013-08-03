<?php

/**
 * Description of Storage
 *
 * @author Bezalel
 */
namespace BzlMail\Settings\Storage;
use BzlMail\Settings\Storage\Adapter\AdapterInterface;
use BzlMail\Settings\Settings;

class Storage
{
    /**
     *
     * @var AdapterInterface
     */
    protected $adapter;
    
    /**
     * @param \BzlMail\Settings\Storage\Adapter\AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter = null)
    {
        if($adapter !== null)
            $this->setAdapter($adapter);
    }
    
    /**
     * @param \BzlMail\Settings\Storage\Adapter\AdapterInterface $adapter
     */
    public function setAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }
    
    /**
     * @param Settings $settings
     */
    public function save(Settings $settings)
    {
        $this->adapter->saveSettings($settings);
    }
    
    /**
     * @return Settings 
     */
    public function get()
    {
        return $this->adapter->getSettings();
    }
}
