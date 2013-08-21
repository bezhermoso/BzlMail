<?php

/**
 * Description of IndexController
 *
 * @author Bezalel
 */
namespace BzlMail\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use BzlMail\Service;

class IndexController extends AbstractActionController
{
    protected $service;
    protected $transportForm;
    protected $session;
    
    /**
     * @return Service\BzlMail
     */
    public function getService()
    {
        if($this->service === null){
            $this->service = $this->getServiceLocator()->get('BzlMail\Service\BzlMail');
        }
        return $this->service;
    }
    
    public function getSessionContainer()
    {
        if ($this->session === null) {
            $this->session = new \Zend\Session\Container('bzlmail_transport_selection');
        }
        return $this->session;
    }
    
    public function getTransportForm()
    {
        if ($this->transportForm === null) {
            $this->transportForm = new \BzlMail\Form\TransportOptions($this->getService()->getTransportOptions());
        }
        return $this->transportForm;
    }
    
    public function settingsAction()
    {
        $service = $this->getService();
        $form = $this->getTransportForm();
        $settings = $service->getSettings();
        
        if ($settings) {
            $form->get('transport')->setValue($settings->getTransport());
        }
        
        $form->setAttribute('action', $this->url()->fromRoute('bzl-mail/process-settings'));
        $form->setAttribute('method', 'post');
        return array(
            'form' => $form,
            'options' => $service->getTransportOptions(),
            'settings' => $service->getSettings(),
        );
    }
    
    public function processSettingsAction()
    {
        $transport = $this->params()->fromPost('transport');
        
        $form = $this->getTransportForm();
        
        $form->setData($this->params()->fromPost());
        
        if ($form->isValid()) {
                        
            $transport = $form->get('transport')->getValue();
            if ($transport) {
                
                $service = $this->getService();
                $transportOptions = $service->getTransportOptions();

                if (isset($transportOptions[$transport])) {
                    $option = $transportOptions[$transport];
                    /* @var $option \BzlMail\Transport\Option\AbstractOption */
                    if ($option->getForm()) {
                        $this->flashMessenger()->addInfoMessage('Almost there. Please provide the information required to complete this set-up.');
                        return $this->prg($this->url()->fromRoute('bzl-mail/transport-settings'), true);
                    } else {
                        $service->setChosenOption($option);
                        $this->flashMessenger()->addSuccessMessage('Settings saved. No further set-up required.');
                    }
                }     
            }
        } else {
            foreach ($form->getInputFilter()->getInvalidInput() as $input) {
                $this->flashMessenger()->addErrorMessage($input->getErrorMessage());
            }
        }
        
        return $this->redirect()->toRoute('bzl-mail/settings');            
    }
    
    public function transportSettingsAction()
    {
        $data = $this->prg();
        
        $model = new \Zend\View\Model\ViewModel();
        
        if ($data) {
            
            $transport = $data['transport'];
            
            $service = $this->getService();
            $settings = $service->getSettings();
            
            $transportOptions = $service->getTransportOptions();
            
            $option = $transportOptions[$transport];
            
            /* @var $option \BzlMail\Transport\Option\AbstractOption */
            
            if (
                count($data) == 1
                && !isset($data['__form_validation_failure__'])
                && $settings
                && $settings->getTransport() == $transport
            ) {
                $option->setSettings($settings->getSettings());
            }
            
            $model->option = $option;
            
            $option->getForm()->add(array(
                'name' => 'transport',
                'attributes' => array(
                    'type' => 'hidden',
                )
            ));
            
            $option->getForm()->setData($data);
            
            $option->getForm()->setAttribute('action', $this->url()->fromRoute('bzl-mail/process-transport-settings'));
            
        } else {
            return $this->redirect()->toRoute('bzl-mail/settings');
        }
        
        return $model;
    }
    
    public function processTransportSettingsAction()
    {
        $data = $this->params()->fromPost();
        
        $service = $this->getService();
        
        $transportOptions = $service->getTransportOptions();
        
        if(isset($data['transport']) && $data['transport'] && isset($transportOptions[$data['transport']])){
            
            $option = $transportOptions[$data['transport']];
            
            $option->getForm()->setData($data);
            
            if($option->getForm()->isValid()){
                
                if($data['action'] == 'save'){
                    try{
                        $service->setChosenOption($option);
                        $this->flashMessenger()->addSuccessMessage('Settings saved.');
                        return $this->redirect()->toRoute('bzl-mail/settings');
                    }catch(\BzlMail\Settings\Storage\Exception\RuntimeException $e){
                        $this->flashMessenger()->addErrorMessage('There was an error encountered when saving your settings was attempted. Please check with the administrator.');
                    }
                } elseif ($data['action'] == 'test') {
                    $emailValidator = new \Zend\Validator\EmailAddress();
                    if($emailValidator->isValid($data['send_test_to'])){
                        $transport = $option->getTransport();
                        $message = new \Zend\Mail\Message();
                        $message->setTo($data['send_test_to'])
                                ->setSubject('BzlMail - Testing ' . $option->getName() . ' Settings')
                                ->setFrom('noreply@bez.im', 'BzlMail')
                                ->setBody('If you are seeing this, congratulations!');
                        try{
                            $transport->send($message);
                            $this->flashMessenger()->addSuccessMessage('Test email sent to ' . $data['send_test_to']);
                        }catch(\Exception $e){
                            $this->flashMessenger()->addErrorMessage($e->getMessage());
                        }
                    }else{
                        foreach($emailValidator->getMessages() as $message){
                            $this->flashMessenger()->addErrorMessage($message);
                        }
                    }
                }
                
            }else{
                foreach($option->getForm()->getInputFilter()->getInvalidInput() as $input){
                    foreach($input->getMessages() as $message){
                        $this->flashMessenger()->addErrorMessage($message);
                    }
                }
                $this->getRequest()->getPost()->set('__form_validation_failure__', 1);
            }
        }
        return $this->prg($this->url()->fromRoute('bzl-mail/transport-settings'), true);
        
        return $this->getResponse();
    }
}
