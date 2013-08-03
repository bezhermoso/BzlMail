<?php

/**
 * Description of TransportOptions
 *
 * @author Bezalel
 */

namespace BzlMail\Transport;

use Zend\ServiceManager\ServiceLocatorInterface;
class TransportOptions implements \Iterator, \ArrayAccess
{
    protected $options;
    
    protected $counter;

    protected $serviceLocator;
    
    public function __construct($options, ServiceLocatorInterface $serviceLocator)
    {
        $this->counter = 0;
        $this->options = $options;
        $this->setServiceLocator($serviceLocator);
    }
    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    
    /**
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
    
    private function prepareOption($key)
    {
        $option = $this->options[$key];
        
        if(is_string($option)){
            
            if($this->getServiceLocator()->has($option)){
                $optionObject = $this->getServiceLocator()->get($option);
            }elseif(class_exists($option)){
                $optionObject = new $option();
            }else{
                throw new \Exception($option . ' cannot be resolved.');
            }
            if($optionObject instanceof Option\AbstractOption){
                $this->options[$key] = $optionObject;
            }else{
                throw new \InvalidArgumentException($option . ' does not extend BzlMail\Transport\Option\AbstractOption');
            }
        }
    }

    public function current()
    {
        $key = $this->key();
        $this->prepareOption($key);
        return $this->options[$key];
    }

    public function key()
    {
        return key($this->options);
    }

    public function next()
    {
        next($this->options);
        return $this->counter++;
    }

    public function offsetExists($offset)
    {
        return isset($this->options[$offset]);
    }

    public function offsetGet($offset)
    {
        $this->prepareOption($offset);
        return $this->options[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->options[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        throw new \Exception('Unsetting of transport option not allowed.');
    }

    public function rewind()
    {
        reset($this->options);
        $this->counter = 0;
    }

    public function valid()
    {
        return $this->counter < count($this->options);
    }
}
