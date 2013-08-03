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

class BzlMailServiceTest extends \PHPUnit_Framework_TestCase
{
    protected $serviceManager;
    
    public function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();
    }
}
