<?php

define("_CRONJOB_",true);
require("public/index.php");

require $init;

$app = Zend\Mvc\Application::init(require 'config/application.php');

$serviceManager = $app->getServiceManager();

$bookingManager = $serviceManager->get('Booking\Manager\BookingManager');
$eventManager = $serviceManager->get('Event\Manager\EventManager');
$reservationManager = $serviceManager->get('Booking\Manager\ReservationManager');
$userManager = $serviceManager->get('User\Manager\UserManager');
$userMailService = $serviceManager->get('User\Service\MailService');
$optionManager = $serviceManager->get('Base\Manager\OptionManager');

$daysToRender = 1;
$dayExceptions = [];
$dayExceptionsExceptions = [];

$dateStart = new DateTime();
$dateStart->setTime(0,0,0);

$dateEnd = clone $dateStart;
$dateEnd->modify('+0 day');
$dateEnd->setTime(23, 59, 59);

$reservations = $reservationManager->getInRange($dateStart, $dateEnd);
$bookings = $bookingManager->getByReservations($reservations);
$events = $eventManager->getInRange($dateStart, $dateEnd);

$usersToNotify = $userManager->getByBookings($bookings);

foreach ($usersToNotify as $user) {
  $subject = "Seminarraum Pin";
  $text = sprintf("die heutige Pin lautet %s. Vielen Dank für Ihre Buchung.",
    $optionManager->get('service.calendar.tresor-pin'));
  $userMailService->send($user, $subject, $text);
}

?>
