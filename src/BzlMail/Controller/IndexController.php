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
        if($this->session === null){
            $this->session = new \Zend\Session\Container('bzlmail_transport_selection');
        }
        return $this->session;
    }
    
    public function getTransportForm()
    {
        if($this->transportForm === null){
            $this->transportForm = new \BzlMail\Form\TransportOptions($this->getService()->getTransportOptions());
        }
        return $this->transportForm;
    }
    
    public function settingsAction()
    {
        $form = $this->getTransportForm();
        $form->setAttribute('action', $this->url()->fromRoute('bzl-mail/process-settings-1'));
        $form->setAttribute('method', 'post');
        return array(
            'form' => $form,
            'options' => $this->getService()->getTransportOptions(),
        );
    }
    
    public function processSettings1Action()
    {
        $transport = $this->params()->fromPost('transport');
        
        $form = $this->getTransportForm();
        
        $form->setData($this->params()->fromPost());
        
        if($form->isValid()){
                        
            $transport = $form->get('transport')->getValue();
            if($transport){
                
                $service = $this->getService();
                $transportOptions = $service->getTransportOptions();

                if(isset($transportOptions[$transport])){
                    $option = $transportOptions[$transport];
                    /* @var $option \BzlMail\Transport\Option\AbstractOption */
                    if($option->getForm()){
                        return $this->prg($this->url()->fromRoute('bzl-mail/transport-settings'), true);
                    }else{
                        $settings = new \BzlMail\Settings\Settings($transport, null);
                        $service->saveSettings($settings);
                        //Success message.
                    }
                }        
            }
        }else{
            foreach($form->getInputFilter()->getInvalidInput() as $input){
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
            $transportOptions = $service->getTransportOptions();
            
            $option = $transportOptions[$transport];
            
            /* @var $option \BzlMail\Transport\Option\AbstractOption */
            
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
                return $this->redirect()->toRoute('bzl-mail/settings');
                
            }else{
                foreach($option->getForm()->getInputFilter()->getInvalidInput() as $input){
                    foreach($input->getMessages() as $message){
                    }
                }
                exit;
                return $this->prg($this->url()->fromRoute('bzl-mail/transport-settings'), true);
            }
        }
        
        
        return $this->getResponse();
    }
}
