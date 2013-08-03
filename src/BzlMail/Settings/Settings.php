<?php


/**
 * Description of Settings
 *
 * @author Bezalel
 */

namespace BzlMail\Settings;

class Settings
{
    protected $transport;
    protected $settings;
    
    public function __construct($transport, $settings)
    {
        $this->transport = $transport;
        $this->settings = $settings;
    }
    
    public function getTransport()
    {
        return $this->transport;
    }
    
    public function getSettings()
    {
        return $this->settings;
    }
}
