<?php

return array(
    /* Configuration options for BzlMail. */
    'bzl-mail' => array(
        /* Transport options available for selection. */
        'transport_options' => array(
            'sendmail' => 'BzlMail\Transport\Option\Sendmail',
            'smtp' => 'BzlMail\Transport\Option\Smtp',
            'gmailSmtp' => 'BzlMail\Transport\Option\GmailSmtp',
        ),
        'settings_storage_adapter' => 'BzlMail\Settings\Storage\Adapter\JsonConfig',
        
        /* Settings for BzlMail\Composition\Facade | bzlSend controller plugin. */
        'composition_facade' => array(
            'sender_email' => 'noreply@domain.com',
            'sender_name' => 'No Reply',
            'transport' => 'bzlmail.transport',
        )
    ),
    'service_manager' => array(
        'factories' => array(
            'BzlMail\Service\BzlMail' => 'BzlMail\Service\BzlMailFactory',
            'BzlMail\Config' => 'BzlMail\Service\ConfigFactory',
            'bzlmail.transport' => 'BzlMail\Service\TransportFactory',
            'bzlmail.composer' => 'BzlMail\Service\CompositionFacadeFactory',
        ),
        'shared' => array(
            'bzlmail.transport' => false,
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'BzlMail\Controller\Index' => 'BzlMail\Controller\IndexController',
        ),
    ),
    'controller_plugins' => array(
        'invokables' => array(
            'bzlTransport' => 'BzlMail\Controller\Plugin\BzlTransport',
            'bzlSend' => 'BzlMail\Controller\Plugin\BzlSend',
        )
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
                    'process-settings' => array(
                        'type' => 'literal',
                        'options' => array(
                            'route' => '/process-settings',
                            'defaults' => array(
                                'controller' => 'BzlMail\Controller\Index',
                                'action' => 'process-settings',
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
