<?php

namespace Booking\Service\Listener;

use Backend\Service\MailService as BackendMailService;
use Base\Manager\OptionManager;
use Base\View\Helper\DateRange;
use Booking\Manager\ReservationManager;
use Square\Manager\SquareManager;
use User\Manager\UserManager;
use User\Entity\User;
use User\Service\MailService as UserMailService;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\I18n\View\Helper\DateFormat;

class NotificationListener extends AbstractListenerAggregate
{

    protected $optionManager;
    protected $reservationManager;
    protected $squareManager;
    protected $userManager;
    protected $userMailService;
	protected $backendMailService;
    protected $dateFormatHelper;
    protected $dateRangeHelper;
    protected $translator;

    public function __construct(OptionManager $optionManager, ReservationManager $reservationManager, SquareManager $squareManager,
	    UserManager $userManager, UserMailService $userMailService, BackendMailService $backendMailService,
	    DateFormat $dateFormatHelper, DateRange $dateRangeHelper, TranslatorInterface $translator)
    {
        $this->optionManager = $optionManager;
        $this->reservationManager = $reservationManager;
        $this->squareManager = $squareManager;
        $this->userManager = $userManager;
        $this->userMailService = $userMailService;
	    $this->backendMailService = $backendMailService;

        $this->dateFormatHelper = $dateFormatHelper;
        $this->dateRangeHelper = $dateRangeHelper;
        $this->translator = $translator;
    }

    public function attach(EventManagerInterface $events)
    {
        $events->attach('create.booking', array($this, 'onCreate'));
        $events->attach('cancel.booking', array($this, 'onCancel'));
    }

    public function validateUser(User $user)
    {
        if($user->get('email', null) == null)
            return false;

        //need to check if we are admins or assists and want to receive notifications
        if($user->getStatus() == 'Admin' || $user->getStatus() == 'Assist')
            if(!$this->optionManager->get('client.contact.email.admin-notifications'))
                return false;

        return true;
    }

    public function onCreate(Event $event)
    {
        $booking = $event->getTarget();
        $square = $this->squareManager->get($booking->need('sid'));
        $user = $this->userManager->get($booking->need('uid'));
 
        $dateFormatHelper = $this->dateFormatHelper;
        $dateRangerHelper = $this->dateRangeHelper;

        if(!$this->validateUser($user))
            return;

        $reservationArray = $this->reservationManager->getBy(['bid' => $booking->need('bid')], 'date ASC');
 
        //for single bookins we only have one reservation, so array will have one element, need to get the first
        $reservation = reset($reservationArray);

	    $reservationTimeStart = explode(':', $reservation->need('time_start'));
        $reservationTimeEnd = explode(':', $reservation->need('time_end'));

        $reservationStart = new \DateTime($reservation->need('date'));
        $reservationStart->setTime($reservationTimeStart[0], $reservationTimeStart[1]);

        $reservationEnd = new \DateTime($reservation->need('date'));
        $reservationEnd->setTime($reservationTimeEnd[0], $reservationTimeEnd[1]);

        $subject = sprintf($this->t('Your %s-booking for %s'),
            $this->optionManager->get('subject.square.type'),
            $dateFormatHelper($reservationStart, \IntlDateFormatter::MEDIUM, \IntlDateFormatter::SHORT));

        $message = sprintf($this->t('we have reserved %s %s, %s for you. Thank you for your booking.'),
            $this->optionManager->get('subject.square.type'),
            $square->need('name'),
            $dateRangerHelper($reservationStart, $reservationEnd));

        if($booking->getMeta('repeat')) {
            $lastReservation = end($reservationArray);
            $reservationEnd = new \DateTime($lastReservation->need('date'));

            $message .= "\n".$this->t("This booking repeats:")."\n";
            $message .= $this->t($booking->getRepeat())." ".$this->t("until")." ";
            $message .= $dateFormatHelper($reservationEnd, \IntlDateFormatter::MEDIUM, \IntlDateFormatter::NONE).".";
        }
        
        $playerNames = $booking->getMeta('player-names');

        if ($playerNames) {
            $playerNamesUnserialized = @unserialize($playerNames);

            if (is_iterable($playerNamesUnserialized)) {
                $message .= "\n\nAngegebene Mitspieler:";

                foreach ($playerNamesUnserialized as $i => $playerName) {
                    $message .= sprintf("\n%s. %s",
                        $i + 1, $playerName['value']);
                }
            }
        }

        if ($square->get('allow_notes') && $booking->getMeta('notes')) {
            $message .= "\n\nAnmerkungen:";
            $message .= "\n" . $booking->getMeta('notes');
        }

        if ($user->getMeta('notification.bookings', 'true') == 'true') {
            $this->userMailService->send($user, $subject, $message);
        }

	    if ($this->optionManager->get('client.contact.email.user-notifications')) {

		    $backendSubject = sprintf($this->t('%s\'s %s-booking for %s'),
		        $user->need('alias'), $this->optionManager->get('subject.square.type'),
			    $dateFormatHelper($reservationStart, \IntlDateFormatter::MEDIUM, \IntlDateFormatter::SHORT));

		    $addendum = sprintf($this->t('Originally sent to %s (%s).'),
	            $user->need('alias'), $user->need('email'));

	        $this->backendMailService->send($backendSubject, $message, array(), $addendum);
        }
    }

    public function onCancel(Event $event)
    {
        $booking = $event->getTarget();
        $reservations = $this->reservationManager->getBy(['bid' => $booking->need('bid')], 'date ASC');
        $reservation = current($reservations);
        $lastReservation = end($reservations);
        $square = $this->squareManager->get($booking->need('sid'));
        $user = $this->userManager->get($booking->need('uid'));

        if(!$this->validateUser($user))
            return;

        $dateRangerHelper = $this->dateRangeHelper;

	    $reservationTimeStart = explode(':', $reservation->need('time_start'));
        $reservationTimeEnd = explode(':', $reservation->need('time_end'));

        $reservationStart = new \DateTime($reservation->need('date'));
        $reservationStart->setTime($reservationTimeStart[0], $reservationTimeStart[1]);

        if($booking->getMeta('repeat')) {
            $reservationEnd = new \DateTime($lastReservation->need('date'));
        }
        else {
            $reservationEnd = new \DateTime($reservation->need('date'));
        }

        $reservationEnd->setTime($reservationTimeEnd[0], $reservationTimeEnd[1]);

        $subject = sprintf($this->t('Your %s-booking has been cancelled'),
            $this->optionManager->get('subject.square.type'));

        $message = sprintf($this->t('we have just cancelled %s %s, %s for you.'),
            $this->optionManager->get('subject.square.type'),
            $square->need('name'),
            $dateRangerHelper($reservationStart, $reservationEnd));

        if($booking->getMeta('repeat')) {
                $message .= "\n".$this->t("This booking was a repeat booking: ");
                $message .= $this->t($booking->getRepeat());
        }

        if ($user->getMeta('notification.bookings', 'true') == 'true') {
            $this->userMailService->send($user, $subject, $message);
        }

	    if ($this->optionManager->get('client.contact.email.user-notifications')) {

		    $backendSubject = sprintf($this->t('%s\'s %s-booking has been cancelled'),
		        $user->need('alias'), $this->optionManager->get('subject.square.type'));

		    $addendum = sprintf($this->t('Originally sent to %s (%s).'),
	            $user->need('alias'), $user->need('email'));

	        $this->backendMailService->send($backendSubject, $message, array(), $addendum);
        }
    }

    protected function t($message)
    {
        return $this->translator->translate($message);
    }

}
