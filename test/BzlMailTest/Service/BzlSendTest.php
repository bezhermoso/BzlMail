<?php

/**
 * Description of BzlSendTest
 *
 * @author Bezalel
 */
namespace BzlMailTest\Service;

use BzlMail\Service;
use BzlMail\Settings;
use BzlMail\Transport;
use Zend\Config;
use Zend\Mail;
use BzlMailTest\Bootstrap;
use BzlMail\Controller\Plugin\BzlSend;

class BzlSendTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BzlSend 
     */
    protected $plugin;
    
    protected $serviceManager;
    
    /**
     *
     * @var Mail\Transport\File
     */
    protected $fileTransport;
    
    public function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();
        $this->plugin = $this->serviceManager->get('ControllerPluginManager')->get('BzlSend');
        
        $fileOptions = new Mail\Transport\FileOptions(array(
            'path' => 'mailbox'
        ));
        
        $this->fileTransport = new Mail\Transport\File($fileOptions);
        $this->plugin->setTransport($this->fileTransport);
        
    }
    
    public function testComposition()
    {
        $this->assertInstanceOf('BzlMail\Controller\Plugin\BzlSend', $this->plugin);
        
        $this->plugin->addAttachment('test', 'test.txt', 'text/plain', true)
                     ->addAttachment('settings.json', 'application/json')
                     ->setSubject('Test')
                     ->setContent('content', 'text/plain')
                     ->send();
    }
}
