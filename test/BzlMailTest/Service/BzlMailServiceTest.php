<?php

/**
 * Description of BzlMailServiceTest
 *
 * @author Bezalel
 */

namespace BzlMailTest\Service;

use BzlMail\Service;
use BzlMail\Settings;
use BzlMail\Transport;
use Zend\Config;
use BzlMailTest;
use BzlMailTest\Bootstrap;

class BzlMailServiceTest extends \PHPUnit_Framework_TestCase
{
    protected $serviceManager;
    
    public function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();
        
        if(file_exists('settings.json')){
            unlink('settings.json');
        }
        
    }
    
    public function testBundledTransportOptionsExist()
    {
        $config = new Config\Config(array(
            'default_transport' => 'sendmail',
            'transport_options' => array(
                'sendmail' => 'BzlMail\Transport\Option\Sendmail',
                'smtp' => 'BzlMail\Transport\Option\Smtp',
            )
        ));
        
        $service = new Service\BzlMail($config, $this->serviceManager);
        
        $transportOptions = $service->getTransportOptions();
        
        $this->assertInstanceOf('BzlMail\Transport\TransportOptions', $transportOptions);
        
        foreach($transportOptions as $key => $option){
            
            $this->assertInstanceOf('BzlMail\Transport\Option\AbstractOption', 
                                    $option,
                                    get_class($option) . ' must extend BzlMail\Transport\Option\AbstractOption');
        
            $this->assertInstanceOf($config->transport_options->$key, 
                                    $option,
                                    'Option ' . $key . ' does not contain an appropriate object.');
            
        }
    }
    
    public function testDefaultTransportOptionCanBeResolved()
    {
        $config = new Config\Config(array(
            'default_transport' => 'sendmail',
            'transport_options' => array(
                'sendmail' => 'BzlMail\Transport\Option\Sendmail',
                'smtp' => 'BzlMail\Transport\Option\Smtp',
            )
        ));
        
        $service = new Service\BzlMail($config, $this->serviceManager);
        
        $defaultOption = $service->getDefaultTransportOption();
        
        $this->assertInstanceOf($config->transport_options->sendmail, 
                                $defaultOption);
    }
    
    public function testJsonConfigStorageAdapter()
    {
        $config = new Config\Config(array(
            'default_transport' => 'sendmail',
            'transport_options' => array(
                'sendmail' => 'BzlMail\Transport\Option\Sendmail',
                'smtp' => 'BzlMail\Transport\Option\Smtp',
            )
        ));
        $service = new Service\BzlMail($config, $this->serviceManager);
        $settings = new Settings\Settings('smtp', array(
            'username' => 'bezhermoso',
            'password' => 'password'
        ));
        $service->setStorage(new Settings\Storage\Storage(new Settings\Storage\Adapter\JsonConfig('settings.json')));
        $service->saveSettings($settings);
        
        $this->assertFileExists('settings.json');
        
        $option = $service->getChosenTransportOption(false);
        $this->assertInstanceOf($config->transport_options->smtp, $option);
        $this->assertInstanceOf('Zend\Mail\Transport\Smtp', $option->getTransport());
    }
}
