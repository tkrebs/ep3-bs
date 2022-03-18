<?php

return array(
    'router' => array(
        'routes' => array(
            'user' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/user',
                ),
                'may_terminate' => false,
                'child_routes' => array(
                    'login' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/login',
                            'defaults' => array(
                                'controller' => 'User\Controller\Session',
                                'action' => 'login',
                            ),
                        ),
                    ),
                    'logout' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/logout',
                            'defaults' => array(
                                'controller' => 'User\Controller\Session',
                                'action' => 'logout',
                            ),
                        ),
                    ),
                    'password' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/password',
                            'defaults' => array(
                                'controller' => 'User\Controller\Account',
                                'action' => 'password',
                            ),
                        ),
                    ),
                    'password-reset' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/password-reset',
                            'defaults' => array(
                                'controller' => 'User\Controller\Account',
                                'action' => 'passwordReset',
                            ),
                        ),
                    ),
                    'registration' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/registration',
                            'defaults' => array(
                                'controller' => 'User\Controller\Account',
                                'action' => 'registration',
                            ),
                        ),
                    ),
                    'registration-confirmation' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/registration-confirmation',
                            'defaults' => array(
                                'controller' => 'User\Controller\Account',
                                'action' => 'registrationConfirmation',
                            ),
                        ),
                    ),
                    'activation' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/activation',
                            'defaults' => array(
                                'controller' => 'User\Controller\Account',
                                'action' => 'activation',
                            ),
                        ),
                    ),
                    'activation-resend' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/activation-resend',
                            'defaults' => array(
                                'controller' => 'User\Controller\Account',
                                'action' => 'activationResend',
                            ),
                        ),
                    ),
                    'bookings' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/bookings',
                            'defaults' => array(
                                'controller' => 'User\Controller\Account',
                                'action' => 'bookings',
                            ),
                        ),
                        'may_terminate' => true,
                        'child_routes' => array(
                            'bills' => array(
                                'type' => 'Segment',
                                'options' => array(
                                    'route' => '/bills/:bid',
                                    'defaults' => array(
                                        'controller' => 'User\Controller\Account',
                                        'action' => 'bills',
                                    ),
                                    'constraints' => array(
                                        'bid' => '[0-9]+',
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'settings' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/settings',
                            'defaults' => array(
                                'controller' => 'User\Controller\Account',
                                'action' => 'settings',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),

    'controllers' => array(
        'invokables' => array(
            'User\Controller\Session' => 'User\Controller\SessionController',
            'User\Controller\Account' => 'User\Controller\AccountController',
        ),
    ),

    'controller_plugins' => array(
        'factories' => array(
            'Authorize' => 'User\Controller\Plugin\AuthorizeFactory',
        ),
    ),

    'service_manager' => array(
        'factories' => array(
            'User\Manager\UserManager' => 'User\Manager\UserManagerFactory',
            'User\Manager\UserSessionManager' => 'User\Manager\UserSessionManagerFactory',

            'User\Table\UserMetaTable' => 'User\Table\UserMetaTableFactory',
            'User\Table\UserTable' => 'User\Table\UserTableFactory',

            'User\Service\MailService' => 'User\Service\MailServiceFactory',

            'Zend\Session\Config\ConfigInterface' => 'Zend\Session\Service\SessionConfigFactory',
            'Zend\Session\SessionManager' => 'Zend\Session\Service\SessionManagerFactory',
        ),
    ),

    'form_elements' => array(
        'factories' => array(
            'User\Form\EditEmailForm' => 'User\Form\EditEmailFormFactory',
            'User\Form\RegistrationForm' => 'User\Form\RegistrationFormFactory',
        ),
    ),

    'view_helpers' => array(
        'factories' => array(
            'UserLastBookings' => 'User\View\Helper\LastBookingsFactory',
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);