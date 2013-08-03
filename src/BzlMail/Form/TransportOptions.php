<?php

/**
 * Description of TransportOptions
 *
 * @author Bezalel
 */

namespace BzlMail\Form;

use Zend\Form;
use Zend\InputFilter;

class TransportOptions extends Form\Form
{
    public function __construct(\BzlMail\Transport\TransportOptions $transportOptions)
    {
        parent::__construct('transport_options');
        $input = new Form\Element\Radio('transport');
        $options = array();
        foreach($transportOptions as $key => $option){
            $options[$key] = $option->getName();
        }
        $input->setValueOptions($options);
        
        $button = new Form\Element\Button('submit');
        $button->setAttribute('type', 'submit');
        $button->setLabel('Done');
        $this->add($input);
        $this->add($button);
    }
    
    public function getInputFilter()
    {
        if($this->filter === null){
            
            $filter = new InputFilter\InputFilter();
            
            $input = new InputFilter\Input('transport');
            $input->getValidatorChain()
                  ->addByName('notempty', array(
                      'message' => 'You must select a transport.', 
                  ));
            $filter->add($input);
            $this->filter = $filter;
        }
        return $this->filter;
    }
}
