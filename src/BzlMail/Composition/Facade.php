<?php

namespace BzlMail\Composition;

use Zend\ServiceManager;
use Zend\Mail;
use Zend\Mime;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer;

/**
 * Description of Facade
 *
 * @author Bezalel
 */
class Facade  implements ServiceManager\ServiceLocatorAwareInterface
{
    protected $message;
    
    protected $content;
    
    protected $attachments = array();
    
    protected $serviceLocator;
    
    protected $transport;
    
    protected $emailTemplate = false;
    
    protected $renderer;
    
    protected $contentMimeType;
    
    protected $defaultSenderEmail;
    
    protected $defaultSenderName;
    
    
    public function __invoke()
    {
        return $this;
    }
    
    /**
     * @return Mail\Message
     */
    public function getMessage()
    {
        if ($this->message === null) {
            $this->resetmessage();
        }
        return $this->message;
    }
    
    public function setContent($content, $mimeTypeOrVars = null)
    {
        
        if (is_string($content)) {
            
            $mimeTypeOrVars = $mimeTypeOrVars ?: 'text/plain';
            
            if (is_array($mimeTypeOrVars)) {
                $this->content = new ViewModel($mimeTypeOrVars);
                $this->content->setTemplate($content);
            } elseif(is_string($mimeTypeOrVars)) {
                $this->contentMimeType = $mimeTypeOrVars;
                $this->content = $content;
            }
        } else {
            $this->content = $content;
        }
        
        return $this;
    }
    
    public function addAttachment($attachment, $mimeType = null, $filename = null, $isRaw = false, $encoding = null)
    {
        if ($attachment instanceof Mime\Part) {
            
            $attachment->disposition = Mime\Mime::DISPOSITION_ATTACHMENT;
            $this->attachments[] = $attachment;
            
        } elseif (is_string($attachment)) {
            
            if ($isRaw === true) {
                
                if ($mimeType == null)
                    throw new \DomainException('Mime-type must be provided when passing in raw content.');
                
                if ($filename === null)
                    throw new \DomainException('Filename must be provided when passing in raw content.');
                
                $part = new Mime\Part($attachment);
                $part->type = $mimeType;
                $part->filename = $filename;
                $part->disposition = Mime\Mime::DISPOSITION_ATTACHMENT;
                $part->encoding = $encoding ?: Mime\Mime::ENCODING_8BIT;
                
                $this->attachments[] = $part;
                
            } elseif (file_exists($attachment) && is_readable($attachment)) {
                    
                    $part = new Mime\Part(file_get_contents($attachment));
                    
                    if ($mimeType != null) {
                        $part->type = $mimeType;
                    } elseif(extension_loaded('finfo')) {
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $derivedMime = finfo_file($finfo, $attachment);
                        $part->type = $derivedMime;
                    } else {
                        throw new \RuntimeException(
                                'Cannot detect mime-type. Either provide mime-type or enable "fileinfo" extension in PHP.'
                                );
                    }
                    
                    $part->disposition = Mime\Mime::DISPOSITION_ATTACHMENT;
                    $part->filename = $filename ?: basename($attachment);
                    $part->encoding = $encoding ?: Mime\Mime::ENCODING_8BIT;
                    $this->attachments[] = $part;
                
            } else {
                throw new \DomainException( $attachment . ' cannot be resolved within the file system.');
            }
            
        } elseif(is_array($attachment)) {
            
            if (isset($attachment['content'])) {
                
                $this->addAttachment(
                        $attachment['content'], 
                        isset($attachment['mime_type']) ? $attachment['mime_type'] : null, 
                        isset($attachment['name']) ? $attachment['name'] : null, 
                        true, 
                        isset($attachment['encoding']) ? $attachment['encoding'] : null
                    );
                
            } elseif (isset($attachment['file'])) {
                
                $this->addAttachment(
                        $attachment['file'], 
                        isset($attachment['mime_type']) ? $attachment['mime_type'] : null, 
                        isset($attachment['name']) ? $attachment['name'] : null, 
                        false, 
                        isset($attachment['encoding']) ? $attachment['encoding'] : null
                    );
            }
            
        } else {
            
            throw new \InvalidArgumentException('Parameter 1 must be either instance of Zend\Mime\Part or string.');
            
        }
        
        return $this;
    }
    
    public function setAttachments(array $attachments)
    {
        $this->attachments = array();
        
        foreach ($attachments as $attachment) {
            $this->addAttachment($attachment);
        }
        
        return $this;
    }
    
    public function __call($name, $arguments)
    {
        $message = $this->getMessage();
        if (method_exists($message, $name)) {
            if(preg_match('/^get/', $name)){
                return call_user_func_array(array($message, $name), $arguments);
            }else{
                call_user_func_array(array($message, $name), $arguments);
                return $this;
            }
        }
    }
    
    public function send(array $options = null)
    {
        
        $filter = new \Zend\Filter\Word\UnderscoreToCamelCase();
        
        if ($options !== null) {
            foreach ($options as $optionName => $parameters) {
                $methodName = $filter->filter('set_' . $optionName);
                if (method_exists($this, $methodName)) {
                    call_user_func_array(array($this, $methodName), array($parameters));
                } elseif(method_exists($this->getMessage(), $methodName)) {
                    call_user_func_array(array($this->getMessage(), $methodName), array($parameters));
                }
            }
        }
        
        if ($this->content === null) {
           throw new \DomainException('No content specified. Cannot send.'); 
        }
        
        $this->prepareMessage();
        $message = $this->getMessage();
        
        $this->getTransport()->send($message);
        
        $this->resetMessage();
        $this->resetOptions();
        
    }
    
    public function setTo($toAddressOrList, $name = null)
    {
        $this->getMessage()->setTo($toAddressOrList, $name);
        return $this;
    }
    
    public function setBcc($emailOrAddressList, $name = null)
    {
        $this->getMessage()->setBcc($emailOrAddressList, $name);
        return $this;
    }
    
    public function setCc($emailOrAddressList, $name = null)
    {
        $this->getMessage()->setCc($emailOrAddressList, $name);
        return $this;
    }
    
    public function setDefaultSender($emailAddress, $name = null)
    {
        $this->defaultSenderEmail = $emailAddress;
        
        if ($name !== null)
            $this->defaultSenderName = $name;
        
        return $this;
    }
    
    public function setSender($email, $name)
    {
        $this->getMessage()->setSender($email, $name);
        return $this;
    }
        
    public function prepareMessage()
    {
        if ($this->content instanceof ViewModel) {
            
            $template = $this->getEmailTemplate();
            
            if ($template instanceof ViewModel) {
                $template->addChild($this->content);
                $content = $template;
            } else {
                $content = $this->content;
            }
            
            $mainContent = new Mime\Part($this->getRenderer()->render($content));
            $mainContent->type = 'text/html';
            
        } elseif (is_string($this->content)) {
            
            $mainContent = new Mime\Part($this->content);
            $mainContent->type = $this->contentMimeType ?: 'text/plain';
            
        }
        
        $mimeMessage = new Mime\Message();
        
        foreach ($this->attachments as $attachment) {
            /* @var $attachment Mime\Part */
            $attachment->disposition = Mime\Mime::DISPOSITION_ATTACHMENT;
            $mimeMessage->addPart($attachment);
        }
        
        $mimeMessage->addPart($mainContent);
        
        if (!$this->getMessage()->getSender()) {
            
            if ($this->defaultSenderEmail) {
                $this->getMessage()
                    ->setSender($this->defaultSenderEmail, $this->defaultSenderName); 
            } else {
                throw new \RuntimeException('No sender information can be used.');
            }
        }
        
        $this->getMessage()->setBody($mimeMessage);
        
        return $this;
        
    }
    
    /**
     * @return Mail\Transport\TransportInterface
     */
    public function getTransport()
    {
        if ($this->transport !== null) {
            
            if (is_string($this->transport)) {
                
                if ($this->getServiceLocator()->has($this->transport)) {
                    return $this->getServiceLocator()->get($this->transport);
                } elseif (class_exists($this->transport)) {
                    $className = $this->transport;
                    $class = new $className();
                    if ($class instanceof Mail\Transport\TransportInterface) {
                        $this->transport = $class;
                        return $this->transport;
                    } else {
                        throw new \InvalidArgumentException($className . ' is not of type Zend\Mail\Transport\TransportInterface.');
                    }
                } else {
                    throw new \RuntimeException('Cannot resolve transport ' . $this->transport);
                }
            } else {
                return $this->transport;
            }
            
        } else {
            throw new \RuntimeException('No transport specified.');
        }
    }
    
    public function setTransport($transport)
    {
        if (is_string($transport)) {
            $this->transport = $transport;
        } elseif ($transport instanceof Mail\Transport\TransportInterface) {
            $this->transport = $transport;
        } else {
            throw new \InvalidArgumentException( (is_object($transport) ? get_class($transport) : $transport ) . ' is not an instance of Zend\Mail\Transport\TransportInterface');
            
        }
    }
    
    public function resetMessage()
    {
        $this->message = new Mail\Message();
    }
    
    public function resetOptions()
    {
        $this->attachments = array();
        $this->content = null;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function setServiceLocator(ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    
    public function setEmailTemplate($template)
    {
        $this->emailTemplate = $template;
        return $this;
    }
    
    /**
     * @return boolean|ViewModel
     * @throws \RuntimeException
     */
    public function getEmailTemplate()
    {
        if ($this->emailTemplate != null) {
            
            if ($this->emailTemplate instanceof ViewModel) {
                return $this->emailTemplate;
            } elseif(is_string($this->emailTemplate)) {
                $this->emailTemplate = new ViewModel($this->emailTemplate);
            }
            return $this->emailTemplate;
            
        } else {
            return false;
        }
    }
    
    /**
     * @return Renderer\RendererInterface
     */
    public function getRenderer()
    {
        if ($this->renderer === null) {
            $this->renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        }
        
        return $this->renderer;
    }
}
