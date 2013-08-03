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
        $form->setAttribute('method', 'get');
        return array(
            'form' => $form,
            'options' => $this->getService()->getTransportOptions(),
        );
    }
    
    public function processSettings1Action()
    {
        $transport = $this->params()->fromQuery('transport');
        return $this->getResponse();
    }
}
