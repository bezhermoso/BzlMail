<?php

namespace BzlMail\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager;
use Zend\Mail;
use Zend\Mime;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer;

/**
 * Description of BzlSend
 *
 * @author BHermoso
 */
class BzlSend extends AbstractPlugin implements ServiceManager\ServiceLocatorAwareInterface
{
    protected $message;
    
    protected $content;
    
    protected $attachments = array();
    
    protected $serviceLocator;
    
    protected $transport;
    
    protected $emailTemplate = false;
    
    protected $renderer;
    
    protected $contentMimeType;
    
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
    
    public function setContent($content, $mimeType = 'text/plain')
    {
        $this->content = $content;
        
        if (is_string($content)) {
            $this->contentMimeType = $mimeType;
        }
        
        return $this;
    }
    
    public function addAttachment($attachment, $filename = null, $mimeType = null, $isRaw = false)
    {
        if ($attachment instanceof Mime\Part) {
            
            $attachment->disposition = Mime\Mime::DISPOSITION_ATTACHMENT;
            $this->attachments[] = $attachment;
            
        } elseif (is_string($attachment)) {
            
            if ($isRaw === true) {
                
                if ($mimeType == null)
                    throw new \DomainException('Mime-type must be provided when passing in raw content.');
                
                if ($filename === null)
                    throw new \DomainException('Filename must be provided when passing in raw content');
                
                $part = new Mime\Part($attachment);
                $part->type = $mimeType;
                $part->filename = $filename;
                $part->disposition = Mime\Mime::DISPOSITION_ATTACHMENT;
                
                $this->attachments[] = $part;
                
            } elseif (file_exists($attachment) && is_readable($attachment)) {
                    
                    $part = new Mime\Part(file_get_contents($attachment));
                    
                    if($mimeType != null){
                        $part->type = $mimeType;
                    }else{
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $derivedMime = finfo_file($finfo, $attachment);
                        $part->type = $derivedMime;
                    }
                    
                    $part->disposition = Mime\Mime::DISPOSITION_ATTACHMENT;
                    $part->filename = $filename?: basename($filename);
                    $this->attachments[] = $part;
                
            } else {
                throw new \DomainException( $attachment . ' cannot be resolved within the file system.');
            }
            
        } else {
            
            throw new \InvalidArgumentException('Parameter 1 must be either instance of Zend\Mime\Part or string.');
            
        }
        
        return $this;
    }
    
    public function __call($name, $arguments)
    {
        $message = $this->getMessage();
        if (method_exists($message, $name)) {
            call_user_func_array(array($message, $name), $arguments);
            return $this;
        }
    }
    
    public function send(array $options = null)
    {
        if ($options !== null) {
            
        }
        
        $this->prepareContent();
        $message = $this->getMessage();
        
        $this->getTransport()->send($message);
        
        $this->resetMessage();
        $this->resetOptions();
        
    }
    
    public function prepareContent()
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
        
        $this->getMessage()->setBody($mimeMessage);
        
        return $this;
        
    }
    
    /**
     * @return Mail\Transport\TransportInterface
     */
    public function getTransport()
    {
        if($this->transport !== null)
            return $this->transport;
        
        return $this->getServiceLocator()->get('bzlmail.transport');
    }
    
    public function setTransport(Mail\Transport\TransportInterface $transport)
    {
        $this->transport = $transport;
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
        $this->serviceLocator = $serviceLocator->getServiceLocator();
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
