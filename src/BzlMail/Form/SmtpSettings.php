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
                            'message' => 'You must provide the username.',
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
                'validators' => array(
                    'name' => 'notempty',
                    'options' => array(
                        'message' => 'You must provide the password.',
                    ),
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
                        'name' => 'hostname',
                        'options' => array(
                            'allowed' => Validator\Hostname::ALLOW_ALL,
                            'message' => 'Provide a valid SMTP host.'
                        ),
                    ),
                ),
            )));
            
            $this->filter = $filter;
            
        }
        return $this->filter;
    }
}
