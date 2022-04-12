<?php

return array(
    'router' => array(
        'routes' => array(
            'frontend' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        'controller' => 'Frontend\Controller\Index',
                        'action' => 'index',
                    ),
                ),
            ),
        ),
    ),

    'controllers' => array(
        'invokables' => array(
            'Frontend\Controller\Index' => 'Frontend\Controller\IndexController',
        ),
    ),

    'view_helpers' => array(
        'factories' => array (
            'FrontendSquareGroupList' => 'Frontend\View\Helper\SquareGroupListFactory',
            'FrontendSquareGroupCheck' => 'Frontend\View\Helper\SquareGroupCheckFactory',
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);