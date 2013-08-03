<?php


/**
 * Description of Smtp
 *
 * @author Bezalel
 */
namespace BzlMail\Transport\Option;

use Zend\Mail\Transport\Smtp as SmtpTransport;

class Smtp extends AbstractOption
{
    protected $transport;
    protected $form;
    protected $inputFilter;
    
    public function getName()
    {
        return 'SMTP';
    }

    public function getTransport()
    {
        if($this->transport === null){
            $this->transport = new SmtpTransport();
        }
        return $this->transport;
    }
    
    public function getForm()
    {
        if($this->form === null){
            $this->form = new \BzlMail\Form\SmtpSettings();
        }
        return $this->form;
    }
}
