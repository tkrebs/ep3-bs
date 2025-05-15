<?php

namespace Square\View\Helper;

use Booking\Manager\BookingManager;
use Booking\Manager\ReservationManager;
use DateTime;
use IntlDateFormatter;
use Square\Entity\Square;
use Zend\View\Helper\AbstractHelper;

class TimeBlockChoice extends AbstractHelper
{

    protected $bookingManager;
    protected $reservationManager;

    public function __construct(BookingManager $bookingManager, ReservationManager $reservationManager)
    {
        $this->bookingManager = $bookingManager;
        $this->reservationManager = $reservationManager;
    }

    public function __invoke(DateTime $dateTimeStart, DateTime $dateTimeEnd, Square $square)
    {
        $bookableMax = $square->get('time_block_bookable_max');

        if (! $bookableMax || $bookableMax > 86400) {
            return $this->renderDateBlockChoice($dateTimeStart, $dateTimeEnd, $square);
        } else {
            return $this->renderTimeBlockChoice($dateTimeStart, $dateTimeEnd, $square);
        }
    }

    protected function renderTimeBlockChoice(DateTime $dateTimeStart, DateTime $dateTimeEnd, Square $square)
    {
        $bookable = $square->need('time_block_bookable');
        $bookableMax = $square->need('time_block_bookable_max');

        $bookableMaxRounded = floor($bookableMax / $bookable) * $bookable;

        $dateTimeCheck = clone $dateTimeStart;
        $dateTimeCheck->modify('+' . $bookableMaxRounded . ' sec');

        if ($dateTimeCheck->format('Y-m-d') != $dateTimeStart->format('Y-m-d')) {
            $dateTimeCheck->setTime(0, 0);
        }

        $timeCheckParts = explode(':', $dateTimeCheck->format('H:i'));
        $timeCheck = $timeCheckParts[0] * 3600 + $timeCheckParts[1] * 60;

        if ($timeCheck == 0) {
            $timeCheck = 86400;
        }

        $squareTimeEndParts = explode(':', $square->need('time_end'));
        $squareTimeEnd = $squareTimeEndParts[0] * 3600 + $squareTimeEndParts[1] * 60;

        if ($timeCheck > $squareTimeEnd) {
            $dateTimeCheck->modify('-' . ($timeCheck - $squareTimeEnd) . ' sec');
        }

        if ($dateTimeCheck > $dateTimeEnd) {
            $reservationsDateTimeEnd = $dateTimeCheck;
        } else {
            $reservationsDateTimeEnd = $dateTimeEnd;
        }

        $reservations = $this->reservationManager->getInRange($dateTimeStart, $reservationsDateTimeEnd);
        $bookings = $this->bookingManager->getByReservations($reservations);

        $this->reservationManager->getSecondsPerDay($reservations);

        $capacity = $square->need('capacity');
        $capacityHeterogenic = $square->need('capacity_heterogenic');

        /* Render alternate time select */

        $view = $this->getView();
        $html = '';

        $html .= '<select id="sb-alternate-times" style="display: none; margin-right: 16px;">';

        $walkingTimeStartParts = explode(':', $dateTimeStart->format('H:i'));
        $walkingTimeStart = $walkingTimeStartParts[0] * 3600 + $walkingTimeStartParts[1] * 60;

        $walkingDateTime = clone $dateTimeStart;
        $walkingIndex = 0;

        if ($square->getMeta('pseudo-time-block-bookable', 'false') == 'true') {
            $bookable = $square->need('time_block');
        }

        while ($walkingDateTime < $dateTimeCheck) {
            $walkingDateTime->modify('+' . $bookable . ' sec');
            $walkingIndex++;

            $walkingTimeEndParts = explode(':', $walkingDateTime->format('H:i'));
            $walkingTimeEnd = $walkingTimeEndParts[0] * 3600 + $walkingTimeEndParts[1] * 60;

            $quantity = 0;

            foreach ($reservations as $reservation) {
                $booking = $reservation->needExtra('booking');

                if ($booking->need('status') != 'cancelled' && $booking->need('visibility') == 'public') {
                    if ($booking->need('sid') == $square->need('sid')) {
                        if ($reservation->needExtra('time_start_sec') < $walkingTimeEnd &&
                            $reservation->needExtra('time_end_sec') > $walkingTimeStart) {
                            $quantity += $booking->need('quantity');
                        }
                    }
                }
            }

            if ($capacity > $quantity) {
                if ($quantity && ! $capacityHeterogenic) {
                    break;
                }
            } else {
                break;
            }

            if ($walkingDateTime == $dateTimeEnd) {
                $attr = 'selected="selected"';
            } else {
                $attr = null;
            }

            $value = $walkingDateTime->format('H:i');

            $html .= sprintf('<option value="%s" %s>%s</option>',
                $value, $attr, $view->timeRange($dateTimeStart, $walkingDateTime, '%s to %s'));
        }

        $html .= '</select>';

        if ($walkingIndex <= 1) {
            return null;
        }

        /* Render reload button */

        $url = $view->url('square', [], ['query' => [
            'ds' => $dateTimeStart->format('Y-m-d'),
            'de' => $dateTimeEnd->format('Y-m-d'),
            'ts' => $dateTimeStart->format('H:i'),
            'te' => $dateTimeEnd->format('H:i'),
            's' => $square->need('sid'),
            'f' => 'fb']]);

        $html .= sprintf('<a href="%s" id="sb-reload-button" class="default-button squarebox-internal-link" style="display: none;">%s</a>',
            $url, $view->translate('Update'));

        return $html;
    }

    protected function renderDateBlockChoice(DateTime $dateTimeStart, DateTime $dateTimeEnd, Square $square)
    {
        $view = $this->getView();
        $html = '';

        $html .= '<div id="sb-alternate-date" class="sandbox" data-sb-new-button="' . $view->t('Check new period') . '" style="display: none; margin-bottom: 16px;">';

        $html .= '<div class="gray" style="margin-bottom: 8px;">';
        $html .= '<em>' . $view->t('Change period:') . '</em>';
        $html .= '</div>';

        $html .= '<div><div class="inline-element">';
        $html .= '<label for="sb-date-start-choice" class="inline-label symbolic symbolic-date"><span>' . $view->t('Start date') . '</span></label>';
        $html .= '<input type="text" name="sb-date-start-choice" id="sb-date-start-choice" value="' . $view->dateFormat($dateTimeStart, IntlDateFormatter::MEDIUM) . '" class="inline-label-container datepicker" style="padding-left: 28px; width: 96px;">';
        $html .= '</div>';
        $html .= $this->renderDateBlockTimeChoice($square, 'sb-time-start-choice', $dateTimeStart);
        $html .= '</div>';

        $html .= '<div class="gray" style="margin: 8px 0px;">';
        $html .= '<em>' . $view->t('until') . '</em>';
        $html .= '</div>';

        $html .= '<div><div class="inline-element">';
        $html .= '<label for="sb-date-end-choice" class="inline-label symbolic symbolic-date"><span>' . $view->t('End date') . '</span></label>';
        $html .= '<input type="text" name="sb-date-end-choice" id="sb-date-end-choice" value="' . $view->dateFormat($dateTimeEnd, IntlDateFormatter::MEDIUM) . '" class="inline-label-container datepicker" style="padding-left: 28px; width: 96px;">';
        $html .= '</div>';
        $html .= $this->renderDateBlockTimeChoice($square, 'sb-time-end-choice', $dateTimeEnd);
        $html .= '</div>';

        $html .= '</div>';

        return $html;
    }

    protected function renderDateBlockTimeChoice(Square $square, $id, DateTime $timeNow)
    {
        $view = $this->getView();
        $html = '';

        $timeBlockBookable = $square->need('time_block_bookable');

        $timeStartParts = explode(':', $square->need('time_start'));
        $timeStart = $timeStartParts[0] * 3600 + $timeStartParts[1] * 60;

        $timeEndParts = explode(':', $square->need('time_end'));
        $timeEnd = $timeEndParts[0] * 3600 + $timeEndParts[1] * 60;

        if ($timeEnd == 0) {
            $timeEnd = 86400;
        }

        $html .= '<select id="' . $id . '" style="margin-left: 8px;">';

        for ($walkingTime = $timeStart; $walkingTime < $timeEnd; $walkingTime += $timeBlockBookable) {
            $walkingTimeFormat = gmdate('H:i', $walkingTime);

            if ($walkingTimeFormat == $timeNow->format('H:i')) {
                $attr = 'selected="selected"';
            } else {
                $attr = null;
            }

            $html .= '<option value="' . $walkingTimeFormat . '" ' . $attr . '>' . $view->timeFormat($walkingTime, true, 'UTC') . '</option>';
        }

        $html .= '</select>';

        return $html;
    }

}
