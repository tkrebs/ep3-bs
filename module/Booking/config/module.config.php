<?php

return array(
    'service_manager' => array(
        'factories' => array(
            'Booking\Manager\BookingManager' => 'Booking\Manager\BookingManagerFactory',
            'Booking\Manager\Booking\BillManager' => 'Booking\Manager\Booking\BillManagerFactory',
            'Booking\Manager\ReservationManager' => 'Booking\Manager\ReservationManagerFactory',

            'Booking\Service\BookingService' => 'Booking\Service\BookingServiceFactory',
            'Booking\Service\BookingStatusService' => 'Booking\Service\BookingStatusServiceFactory',

            'Booking\Table\BookingMetaTable' => 'Booking\Table\BookingMetaTableFactory',
            'Booking\Table\BookingTable' => 'Booking\Table\BookingTableFactory',
            'Booking\Table\Booking\BillTable' => 'Booking\Table\Booking\BillTableFactory',

            'Booking\Table\ReservationMetaTable' => 'Booking\Table\ReservationMetaTableFactory',
            'Booking\Table\ReservationTable' => 'Booking\Table\ReservationTableFactory',

            /* Listeners */

            'Booking\Service\Listener\NotificationListener' => 'Booking\Service\Listener\NotificationListenerFactory',
        ),
    ),
);
