<?php

return array(
    'service_manager' => array(
        'factories' => array(
            'Event\Manager\EventManager' => 'Event\Manager\EventManagerFactory',

            'Event\Table\EventMetaTable' => 'Event\Table\EventMetaTableFactory',
            'Event\Table\EventTable' => 'Event\Table\EventTableFactory',
        ),
    ),
);