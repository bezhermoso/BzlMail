<?php

return array(
    
    'bzl-mail' => array(
        'default_transport' => 'sendmail',
        'transport_options' => array(
            'sendmail' => 'BzlMail\Transport\Option\Sendmail',
            'smtp' => 'BzlMail\Transport\Option\Smtp',
        )
    ),
    
    'service_manager' => array(
        'factories' => array(
            'BzlMail\Service\BzlMail' => 'BzlMail\Service\BzlMailFactory',
            'BzlMail\Config' => 'BzlMail\Service\ConfigFactory',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'BzlMail\Controller\Index' => 'BzlMail\Controller\IndexController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'bzl-mail' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/email'
                ),
                'may_terminate' => false,
                'child_routes' => array(
                    'settings' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/settings',
                            'defaults' => array(
                                'controller' => 'BzlMail\Controller\Index',
                                'action' => 'settings',
                            )
                        )
                    ),
                    'process-settings-1' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/process-settings-1',
                            'defaults' => array(
                                'controller' => 'BzlMail\Controller\Index',
                                'action' => 'process-settings-1',
                            )
                        )
                    ),
                    'process-transport-settings' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/process-transport-settings',
                            'defaults' => array(
                                'controller' => 'BzlMail\Controller\Index',
                                'action' => 'process-transport-settings',
                            )
                        )
                    ),
                    'transport-settings' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/transport-settings',
                            'defaults' => array(
                                'controller' => 'BzlMail\Controller\Index',
                                'action' => 'transport-settings',
                            )
                        )
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../views',
        )
    )
);
