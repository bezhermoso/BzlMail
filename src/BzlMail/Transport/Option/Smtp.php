<?php


/**
 * Description of Smtp
 *
 * @author Bezalel
 */
namespace BzlMail\Transport\Option;

use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;

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
            
            $settings = $this->getSettings();
            
            $options = array();
            
            
            if($settings){
                
                $options['host'] = $settings['host'];
                $options['port'] = $settings['port'];
                $options['connection_class'] = $settings['authentication'];
                
                $options['connection_config'] = array(
                    'username' => $settings['username'],
                    'password' => $settings['password']
                 );
                
                if(isset($settings['encryption']) && $settings['encryption']){
                    $options['connection_config']['ssl'] = $settings['encryption'];
                }
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
        }
        return $this->form;
    }
}
