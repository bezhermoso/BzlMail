<?php

/**
 * Description of Sendmail
 *
 * @author Bezalel
 */
namespace BzlMail\Transport\Option;

use Zend\Mail\Transport\Sendmail as SendmailTransport;

class Sendmail extends AbstractOption
{
    protected $transport;
    
    public function getName()
    {
        return 'Sendmail';
    }

    public function getTransport()
    {
        if($this->transport === null){
            $this->transport = new SendmailTransport();
        }
        return $this->transport;
    }
}
