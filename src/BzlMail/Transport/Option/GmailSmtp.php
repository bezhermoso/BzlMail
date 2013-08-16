<?php


/**
 * Description of Smtp
 *
 * @author Bezalel
 */
namespace BzlMail\Transport\Option;

use Zend\Mail\Transport\SmtpOptions;
use Zend\Mail\Transport\Smtp as SmtpTransport;

class GmailSmtp extends Smtp
{
    protected $transport;
    protected $form;
    protected $inputFilter;
    
    public function getName()
    {
        return 'SMTP (Gmail)';
    }

    public function getTransport()
    {
        if($this->transport === null){
            $this->transport = new SmtpTransport();
            
            $settings = $this->getSettings();
            
            $options = array();
            
            
            if($settings){
                
                $options['host'] = 'smtp.gmail.com';
                $options['port'] = '587';
                $options['connection_class'] = 'login';
                
                $options['connection_config'] = array(
                    'username' => $settings['username'],
                    'password' => $settings['password'],
                    'ssl' => 'tls',
                 );
            }
            
            $smtpOptions = new SmtpOptions($options);
            $this->transport = new SmtpTransport($smtpOptions);
            
        }
        return $this->transport;
    }
    
    public function getForm()
    {
        if($this->form === null){
            $this->form = new \BzlMail\Form\SmtpSettings();
            $this->form->remove('port')
                       ->remove('host')
                       ->remove('authentication')
                       ->remove('encryption');
            
            $this->form->getInputFilter()
                       ->remove('port')
                       ->remove('host')
                       ->remove('authentication')
                       ->remove('encryption');
        }
        return $this->form;
    }
}
