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
        
        $option = $service->getChosenOption();
        
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
                        $settings = new \BzlMail\Settings\Settings($transport, null);
                        $service->saveSettings($settings);
                        $this->flashMessenger()->addSuccessMessage('Settings saved. No further set-up required.');
                    }
                }        
            }
        }else{
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
        
        if($data){
            
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
            
        }else{
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
                                
                $settings = new \BzlMail\Settings\Settings($data['transport'], $option->getSettings());
                $service->saveSettings($settings);
                $this->flashMessenger()->addSuccessMessage('Settings saved.');
                return $this->redirect()->toRoute('bzl-mail/settings');
                
            }else{
                foreach($option->getForm()->getInputFilter()->getInvalidInput() as $input){
                    foreach($input->getMessages() as $message){
                        $this->flashMessenger()->addErrorMessage($message);
                    }
                }
                $this->getRequest()->getPost()->set('__form_validation_failure__', 1);
                return $this->prg($this->url()->fromRoute('bzl-mail/transport-settings'), true);
            }
        }
        
        return $this->getResponse();
    }
}
