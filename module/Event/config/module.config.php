<?php

return array(
    'router' => array(
        'routes' => array(
            'event' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/event/:eid',
                    'defaults' => array(
                        'controller' => 'Event\Controller\Event',
                        'action' => 'index',
                    ),
                ),
            ),
        ),
    ),

    'controllers' => array(
        'invokables' => array(
            'Event\Controller\Event' => 'Event\Controller\EventController',
        ),
    ),

    'service_manager' => array(
        'factories' => array(
            'Event\Manager\EventManager' => 'Event\Manager\EventManagerFactory',

            'Event\Table\EventMetaTable' => 'Event\Table\EventMetaTableFactory',
            'Event\Table\EventTable' => 'Event\Table\EventTableFactory',
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);