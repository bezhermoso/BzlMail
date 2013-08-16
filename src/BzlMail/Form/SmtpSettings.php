<?php

/**
 * Description of SmtpSettings
 *
 * @author Bezalel
 */
namespace BzlMail\Form;

use Zend\Form;
use Zend\InputFilter;
use Zend\Validator;

class SmtpSettings extends Form\Form
{
    public function __construct()
    {
        parent::__construct('smtp_settings');
        
        $this->add(array(
            'name' => 'username',
            'attributes' => array(
                'type' => 'text',
            ),
            'options' => array(
                'label' => 'Username',
            ),
        ));
        
        $this->add(array(
            'name' => 'password',
            'attributes' => array(
                'type' => 'password',
            ),
            'options' => array(
                'label' => 'Password',
            ),
        ));
        
        $this->add(array(
            'name' => 'host',
            'attributes' => array(
                'type' => 'host',
            ),
            'options' => array(
                'label' => 'Host',
            ),
        ));
        
        $this->add(array(
            'name' => 'port',
            'attributes' => array(
                'type' => 'number',
            ),
            'options' => array(
                'label' => 'Port',
            ),
        ));
        
        $authSelect = new Form\Element\Select('authentication', array(
            'value_options' => array(
                'plain' => 'Plain',
                'login' => 'Login',
                'crammd5' => 'CRAM-MD5',
            )
        ));
        $authSelect->setLabel('Authentication method');
        
        $encryptionSelect = new Form\Element\Select('encryption', array(
            'value_options' => array(
                '' => 'No encryption',
                'tls' => 'TLS',
                'ssl' => 'SSL',
            )
        ));
        $encryptionSelect->setLabel('Encryption');
        
        $this->add($authSelect)
             ->add($encryptionSelect);
        
    }
    
    public function getInputFilter()
    {
        if($this->filter === null){
            
            $filter = new InputFilter\InputFilter();
            $factory = $filter->getFactory();
            
            $filter->add($factory->createInput(array(
                'name' => 'username',
                'filters' => array(
                    array(
                        'name' => 'stringtrim',
                    )
                ),
                'validators' => array(
                    array(
                        'name' => 'notempty',
                        'options' => array(
                            'message' => 'Username can\'t be empty.',
                        ),
                    ),
                ),
            )))->add($factory->createInput(array(
                'name' => 'password',
                'filters' => array(
                    array(
                        'name' => 'stringtrim'
                    )
                ),
            )))->add($factory->createInput(array(
                'name' => 'host',
                'filters' => array(
                    array(
                        'name' => 'stringtrim',
                    )
                ),
                'validators' => array(
                    array(
                        'name' => 'notempty',
                        'options' => array(
                            'message' => 'Host can\'t be empty.',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'hostname',
                        'options' => array(
                            'allow' => Validator\Hostname::ALLOW_ALL,
                            'message' => 'Provide a valid SMTP host.'
                        ),
                    ),
                ),
            )))->add($factory->createInput(array(
                'name' => 'port',
                'filters' => array(
                    array(
                        'name' => 'stringtrim'
                    )
                ),
                'validators' => array(
                    array(
                        'name' => 'notempty',
                        'options' => array(
                            'message' => 'Port can\'t be empty.',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'digits',
                        'options' => array(
                            'message' => 'Provide a valid port.',
                        )
                    )
                )
            )))->add($factory->createInput(array(
                'name' => 'authentication',
                'validators' => array(
                    array(
                        'name' => 'notempty',
                        'options' => array(
                            'message' => 'Select authentication method.',
                        ),
                    ),
                )
            )))->add($factory->createInput(array(
                'name' => 'encryption',
                'required' => false,
            )));
            
            $this->filter = $filter;
            
        }
        return $this->filter;
    }
}
