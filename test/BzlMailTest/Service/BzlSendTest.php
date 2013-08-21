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
        $this->plugin = $this->serviceManager->get('bzlmail.composer');
        
        $fileOptions = new Mail\Transport\FileOptions(array(
            'path' => 'mailbox/new'
        ));
        
        foreach (glob('mailbox/new/*') as $file) {
            unlink($file);
        }
        
        $this->fileTransport = new Mail\Transport\File($fileOptions);
        $this->plugin->setTransport($this->fileTransport);
        
    }
    
    public function testComposition()
    {   
        $this->plugin->setSubject('Subject here.')
                     ->setContent('Content here.', 'text/plain')
                     ->addAttachment('settings.json', 'application/json')
                     ->send();
        $file1 = $this->fileTransport->getLastFile();
        
        $this->plugin->send(array(
            'subject' => 'Subject here.',
            'content' => 'Content here.',
            'attachments' => array(
                array('file' => 'settings.json', 'mime_type' => 'application/json')
            )
        ));
        $file2 = $this->fileTransport->getLastFile();
        
        $box = new Mail\Storage\Maildir(array('dirname' => 'mailbox'));
        
        foreach ($box as $num => $message) {
            foreach (new \RecursiveIteratorIterator($box->getMessage($num)) as $part) {
                var_dump($part->contentType);
            }
        }
        
        
    }
}
