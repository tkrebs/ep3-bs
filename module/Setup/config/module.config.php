<?php

return array(
    'router' => array(
        'routes' => array(
            'setup' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        'controller' => 'Setup\Controller\Index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'tables' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => 'tables',
                            'defaults' => array(
                                'action' => 'tables',
                            ),
                        ),
                    ),
                    'records' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => 'records',
                            'defaults' => array(
                                'action' => 'records',
                            ),
                        ),
                    ),
                    'user' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => 'user',
                            'defaults' => array(
                                'action' => 'user',
                            ),
                        ),
                    ),
                    'complete' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => 'complete',
                            'defaults' => array(
                                'action' => 'complete',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),

    'controllers' => array(
        'invokables' => array(
            'Setup\Controller\Index' => 'Setup\Controller\IndexController',
        ),
    ),

    'controller_plugins' => array(
        'factories' => array(
            'ValidateSetup' => 'Setup\Controller\Plugin\ValidateSetupFactory',
        ),
    ),

    'view_manager' => array(
        'template_map' => array(
            'base/layout'          => __DIR__ . '/../view/layout/layout.phtml',
            'error/404'            => __DIR__ . '/../view/error/404.phtml',
            'error/500'            => __DIR__ . '/../view/error/500.phtml',
        ),

        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);