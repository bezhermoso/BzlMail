<?php
/**
 * Description of AbstractOption
 *
 * @author Bezalel
 */

namespace BzlMail\Transport\Option;
use Zend\Form;
use Zend\Mail\Transport\TransportInterface;

abstract class AbstractOption
{
    /**
     * @return TransportInterface
     */
    abstract public function getTransport();
    
    /**
     * @return string
     */
    abstract public function getName();
    
    /**
     * @return null|Form\Form
     */
    public function getForm()
    {
        return null;
    }
    
    public function getSettings()
    {
        if($this->getForm())
            return $this->getForm()->getData();
    }
    
    public function setSettings($settings)
    {
        if($this->getForm())
            return $this->getForm()->setData($settings);
    }
}
