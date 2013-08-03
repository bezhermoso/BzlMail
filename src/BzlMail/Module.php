<?php

/**
 * Description of Module
 *
 * @author Bezalel
 */
namespace BzlMail;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/../../config/bzl-mail.config.php';
    }
    
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__
                )
            ),
        );
    }
}
