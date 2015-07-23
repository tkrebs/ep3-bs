<?php

namespace Backend\Controller;

use Booking\Entity\Booking;
use Booking\Table\BookingTable;
use Booking\Table\ReservationTable;
use Zend\Db\Adapter\Adapter;
use Zend\Mvc\Controller\AbstractActionController;

class BookingController extends AbstractActionController
{

    public function indexAction()
    {
        $this->authorize('admin.booking');

        $serviceManager = $this->getServiceLocator();
        $bookingManager = $serviceManager->get('Booking\Manager\BookingManager');
        $reservationManager = $serviceManager->get('Booking\Manager\ReservationManager');
        $userManager = $serviceManager->get('User\Manager\UserManager');

        $bookings = array();
        $reservations = array();

        $dateStart = $this->params()->fromQuery('date-start');
        $dateEnd = $this->params()->fromQuery('date-end');
        $search = $this->params()->fromQuery('search');

        if ($dateStart) {
            $dateStart = new \DateTime($dateStart);
        }

        if ($dateEnd) {
            $dateEnd = new \DateTime($dateEnd);
        }

        if (($dateStart && $dateEnd) || $search) {
            $filters = $this->backendBookingDetermineFilters($search);

            try {
                $limit = 1000;

                if ($dateStart && $dateEnd) {
                    $reservations = $reservationManager->getInRange($dateStart, $dateEnd, $limit);
                    $bookings = $bookingManager->getByReservations($reservations, $filters['filters']);
                } else {
                    $bookings = $bookingManager->getBy($filters['filters'], null, $limit);
                    $reservations = $reservationManager->getByBookings($bookings);
                }

                $userManager->getByBookings($bookings);
            } catch (\RuntimeException $e) {
                $bookings = array();
                $reservations = array();
            }
        }

        return array(
            'bookings' => $bookings,
            'reservations' => $reservations,
            'dateStart' => $dateStart,
            'dateEnd' => $dateEnd,
            'search' => $search,
        );
    }

    public function editAction()
    {
        $this->authorize('admin.booking, calendar.see-data');

        $params = $this->backendBookingDetermineParams();

        if (! $this->getRequest()->isPost()) {
            switch (count($params['reservations'])) {
                case 0:
                    $reservation = $booking = null;
                    break;
                case 1:
                    $reservation = current($params['reservations']);
                    $booking = $reservation->getExtra('booking');

                    if ($booking->get('status') == 'subscription') {
                        if (! $params['editMode']) {
                            return $this->forward()->dispatch('Backend\Controller\Booking', ['action' => 'editMode', 'params' => $params]);
                        }
                    }
                    break;
                default:
                    return $this->forward()->dispatch('Backend\Controller\Booking', ['action' => 'editChoice', 'params' => $params]);
            }
        }

        $serviceManager = $this->getServiceLocator();
        $formElementManager = $serviceManager->get('FormElementManager');

        $editForm = $formElementManager->get('Backend\Form\Booking\EditForm');

        if ($this->getRequest()->isPost()) {
            $editForm->setData($this->params()->fromPost());

            if ($editForm->isValid()) {
                $d = $editForm->getData();

                /* Process form (note, that reservation and booking are not available here) */

                if ($d['bf-rid']) {

                    /* Update booking/reservation */

                    $this->backendBookingUpdate($d['bf-rid'], $d['bf-user'], $d['bf-time-start'], $d['bf-time-end'], $d['bf-date-start'],
                        $d['bf-sid'], $d['bf-status-billing'], $d['bf-quantity'], $d['bf-notes']);

                } else {

                    /* Create booking/reservation */

                    $this->backendBookingCreate($d['bf-user'], $d['bf-time-start'], $d['bf-time-end'], $d['bf-date-start'], $d['bf-date-end'],
                        $d['bf-repeat'], $d['bf-sid'], $d['bf-status-billing'], $d['bf-quantity'], $d['bf-notes']);
                }

                $this->flashMessenger()->addSuccessMessage('Booking has been saved');

                return $this->redirect()->toRoute('frontend');
            }
        } else {
            if ($booking) {
                $user = $booking->needExtra('user');

                $editForm->setData(array(
                    'bf-rid' => $reservation->get('rid'),
                    'bf-user' => $user->need('alias') . ' (' . $user->need('uid') . ')',
                    'bf-sid' => $booking->get('sid'),
                    'bf-status-billing' => $booking->get('status_billing'),
                    'bf-quantity' => $booking->get('quantity'),
                    'bf-notes' => $booking->getMeta('notes'),
                ));

                if ($booking->get('status') == 'subscription' && $params['editMode'] == 'booking') {
                    $editForm->setData(array(
                        'bf-time-start' => substr($booking->getMeta('time_start', $reservation->get('time_start')), 0, 5),
                        'bf-time-end' => substr($booking->getMeta('time_end', $reservation->get('time_end')), 0, 5),
                        'bf-date-start' => $this->dateFormat($booking->getMeta('date_start', $reservation->get('date')), \IntlDateFormatter::MEDIUM),
                        'bf-date-end' => $this->dateFormat($booking->getMeta('date_end', $reservation->get('date')), \IntlDateFormatter::MEDIUM),
                        'bf-repeat' => $booking->getMeta('repeat'),
                    ));
                } else {
                    $editForm->setData(array(
                        'bf-time-start' => substr($reservation->get('time_start'), 0, 5),
                        'bf-time-end' => substr($reservation->get('time_end'), 0, 5),
                        'bf-date-start' => $this->dateFormat($reservation->get('date'), \IntlDateFormatter::MEDIUM),
                        'bf-date-end' => $this->dateFormat($reservation->get('date'), \IntlDateFormatter::MEDIUM),
                    ));
                }
            } else {
                $editForm->setData(array(
                    'bf-sid' => $params['square']->get('sid'),
                    'bf-date-start' => $this->dateFormat($params['dateTimeStart'], \IntlDateFormatter::MEDIUM),
                    'bf-date-end' => $this->dateFormat($params['dateTimeEnd'], \IntlDateFormatter::MEDIUM),
                    'bf-time-start' => $params['dateTimeStart']->format('H:i'),
                    'bf-time-end' => $params['dateTimeEnd']->format('H:i'),
                ));
            }
        }

        return $this->ajaxViewModel(array_merge($params, array(
            'editForm' => $editForm,
            'booking' => $booking,
            'reservation' => $reservation,
        )));
    }

    public function editChoiceAction()
    {
        $params = $this->getEvent()->getRouteMatch()->getParam('params');

        return $this->ajaxViewModel($params);
    }

    public function editModeAction()
    {
        $params = $this->getEvent()->getRouteMatch()->getParam('params');

        return $this->ajaxViewModel($params);
    }

    public function editRangeAction()
    {
        $this->authorize('admin.booking, calendar.create-subscription-bookings + calendar.cancel-subscription-bookings');

        $serviceManager = $this->getServiceLocator();
        $bookingManager = $serviceManager->get('Booking\Manager\BookingManager');
        $reservationManager = $serviceManager->get('Booking\Manager\ReservationManager');
        $formElementManager = $serviceManager->get('FormElementManager');

        $bid = $this->params()->fromRoute('bid');

        $booking = $bookingManager->get($bid);

        if ($booking->get('status') != 'subscription') {
            throw new \RuntimeException('Time and date range can only be edited on subscription bookings');
        }

        $editTimeRangeForm = $formElementManager->get('Backend\Form\Booking\Range\EditTimeRangeForm');
        $editDateRangeForm = $formElementManager->get('Backend\Form\Booking\Range\EditDateRangeForm');

        if ($this->getRequest()->isPost()) {
            $db = $serviceManager->get('Zend\Db\Adapter\Adapter');

            $mode = $this->params()->fromQuery('mode');

            if ($mode == 'time') {
                $editTimeRangeForm->setData($this->params()->fromPost());

                if ($editTimeRangeForm->isValid()) {
                    $data = $editTimeRangeForm->getData();

                    $res = $db->query(
                        sprintf('UPDATE %s SET time_start = "%s", time_end = "%s" WHERE bid = %s AND time_start = "%s" AND time_end = "%s"',
                            ReservationTable::NAME,
                            $data['bf-time-start'], $data['bf-time-end'], $bid, $booking->needMeta('time_start'), $booking->needMeta('time_end')),
                        Adapter::QUERY_MODE_EXECUTE);

                    if ($res->getAffectedRows() > 0) {
                        $booking->setMeta('time_start', $data['bf-time-start']);
                        $booking->setMeta('time_end', $data['bf-time-end']);

                        $bookingManager->save($booking);
                    }

                    $this->flashMessenger()->addSuccessMessage('Booking has been saved');

                    return $this->redirect()->toRoute('frontend');
                }
            } else if ($mode == 'date') {
                $editDateRangeForm->setData($this->params()->fromPost());

                if ($editDateRangeForm->isValid()) {
                    $data = $editDateRangeForm->getData();

                    $dateStart = new \DateTime($data['bf-date-start']);
                    $dateEnd = new \DateTime($data['bf-date-end']);
                    $repeat = $data['bf-repeat'];

                    $res = $db->query(
                        sprintf('DELETE FROM %s WHERE bid = %s',
                            ReservationTable::NAME, $bid),
                        Adapter::QUERY_MODE_EXECUTE);

                    if ($res->getAffectedRows() > 0) {
                        $reservationManager->createByRange($booking, $dateStart, $dateEnd,
                            $booking->needMeta('time_start'), $booking->needMeta('time_end'), $repeat);

                        $booking->setMeta('date_start', $dateStart->format('Y-m-d'));
                        $booking->setMeta('date_end', $dateEnd->format('Y-m-d'));
                        $booking->setMeta('repeat', $repeat);

                        $bookingManager->save($booking);
                    }

                    $this->flashMessenger()->addSuccessMessage('Booking has been saved');

                    return $this->redirect()->toRoute('frontend');
                }
            } else {
                throw new \RuntimeException('Invalid edit mode received');
            }
        } else {
            $editTimeRangeForm->setData(array(
                'bf-time-start' => substr($booking->needMeta('time_start'), 0, 5),
                'bf-time-end' => substr($booking->needMeta('time_end'), 0, 5),
            ));

            $editDateRangeForm->setData(array(
                'bf-date-start' => $this->dateFormat($booking->needMeta('date_start'), \IntlDateFormatter::MEDIUM),
                'bf-date-end' => $this->dateFormat($booking->needMeta('date_end'), \IntlDateFormatter::MEDIUM),
                'bf-repeat' => $booking->needMeta('repeat'),
            ));
        }

        return $this->ajaxViewModel(array(
            'booking' => $booking,
            'editTimeRangeForm' => $editTimeRangeForm,
            'editDateRangeForm' => $editDateRangeForm,
        ));
    }

    public function deleteAction()
    {
        $this->authorize('admin.booking, calendar.cancel-single-bookings, calendar.cancel-subscription-bookings');

        $serviceManager = $this->getServiceLocator();
        $bookingManager = $serviceManager->get('Booking\Manager\BookingManager');
        $reservationManager = $serviceManager->get('Booking\Manager\ReservationManager');

        $rid = $this->params()->fromRoute('rid');
        $editMode = $this->params()->fromQuery('edit-mode');

        $reservation = $reservationManager->get($rid);
        $booking = $bookingManager->get($reservation->get('bid'));

        switch ($booking->get('status')) {
            case 'single':
                $this->authorize('admin.booking, calendar.cancel-single-bookings');
                break;
            case 'subscription':
                $this->authorize('admin.booking, calendar.cancel-subscription-bookings');
                break;
        }

        if ($this->params()->fromQuery('confirmed') == 'true') {

            if ($editMode == 'reservation') {

                $reservationManager->delete($reservation);

                $this->flashMessenger()->addSuccessMessage('Reservation has been deleted');
            } else {

                if ($this->params()->fromQuery('cancel') == 'true') {
                    $booking->set('status', 'cancelled');
                    $bookingManager->save($booking);

                    $this->flashMessenger()->addSuccessMessage('Booking has been cancelled');
                } else {
                    $bookingManager->delete($booking);

                    $this->flashMessenger()->addSuccessMessage('Booking has been deleted');
                }
            }

            return $this->redirect()->toRoute('frontend');
        }

        if ($editMode == 'reservation') {
            $template = 'backend/booking/delete.reservation.phtml';
        } else {
            $template = null;
        }

        return $this->ajaxViewModel(array(
            'rid' => $rid,
        ), null, $template);
    }

    public function statsAction()
    {
        $this->authorize('admin.booking');

        $db = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');

        $stats = $db->query(sprintf('SELECT status, COUNT(status) AS count FROM %s GROUP BY status', BookingTable::NAME),
            Adapter::QUERY_MODE_EXECUTE)->toArray();

        return array(
            'stats' => $stats,
        );
    }

    public function billsAction()
    {
        $this->authorize('admin.booking');

        $bid = $this->params()->fromRoute('bid');

        $serviceManager = $this->getServiceLocator();

        $bookingManager = $serviceManager->get('Booking\Manager\BookingManager');
        $bookingBillManager = $serviceManager->get('Booking\Manager\Booking\BillManager');
        $bookingStatusService = $serviceManager->get('Booking\Service\BookingStatusService');
        $userManager = $serviceManager->get('User\Manager\UserManager');

        $booking = $bookingManager->get($bid);
        $bills = $bookingBillManager->getBy(array('bid' => $bid), 'bbid ASC');
        $user = $userManager->get($booking->need('uid'));

        if ($this->getRequest()->isGet()) {
            $create = $this->params()->fromQuery('create');

            if ($create == 'default-bill') {
                $reservationManager = $serviceManager->get('Booking\Manager\ReservationManager');
                $squareManager = $serviceManager->get('Square\Manager\SquareManager');
                $squarePricingManager = $serviceManager->get('Square\Manager\SquarePricingManager');

                $square = $squareManager->get($booking->get('sid'));
                $squareType = $this->option('subject.square.type');
                $squareName = $this->t($square->need('name'));

                $dateRangeHelper = $serviceManager->get('ViewHelperManager')->get('DateRange');

                $created = false;

                foreach ($reservationManager->getBy(['bid' => $bid]) as $reservation) {

                    $dateTimeStart = new \DateTime($reservation->get('date') . ' ' . $reservation->get('time_start'));
                    $dateTimeEnd = new \DateTime($reservation->get('date') . ' ' . $reservation->get('time_end'));

                    $pricing = $squarePricingManager->getFinalPricingInRange($dateTimeStart, $dateTimeEnd, $square, $booking->get('quantity'));

                    if ($pricing) {

                        $description = sprintf('%s %s, %s',
                            $squareType, $squareName,
                            $dateRangeHelper($dateTimeStart, $dateTimeEnd));

                        $bookingBillManager->save(new Booking\Bill(array(
                            'bid' => $bid,
                            'description' => $description,
                            'quantity' => $booking->get('quantity'),
                            'time' => $pricing['seconds'],
                            'price' => $pricing['price'],
                            'rate' => $pricing['rate'],
                            'gross' => $pricing['gross'],
                        )));

                        $created = true;
                    }
                }

                if ($created) {
                    $this->flashMessenger()->addSuccessMessage('Booking-Bill position has been created');
                } else {
                    $this->flashMessenger()->addErrorMessage('No Booking-Bill position has been created');
                }

                return $this->redirect()->toRoute('backend/booking/bills', ['bid' => $bid]);
            }

            $delete = $this->params()->fromQuery('delete');

            if ($delete && is_numeric($delete) && isset($bills[$delete])) {
                $bookingBillManager->delete($delete);

                $this->flashMessenger()->addSuccessMessage('Booking-Bill position has been deleted');
                return $this->redirect()->toRoute('backend/booking/bills', ['bid' => $bid]);
            }
        }

        if ($this->getRequest()->isPost()) {

            /* Check and save billing status */

            $billingStatus = $this->params()->fromPost('ebf-status');

            if ($bookingStatusService->checkStatus($billingStatus)) {
                $booking->set('status_billing', $billingStatus);
                $bookingManager->save($booking);
            } else {
                $this->flashMessenger()->addErrorMessage('Invalid billing status selected');
            }

            /* Check and save known (and new) bills */

            $bills[] = new Booking\Bill(['bid' => $bid]);

            foreach ($bills as $bill) {

                $bbid = $bill->get('bbid', 'new');

                $description = $this->params()->fromPost('ebf-' . $bbid . '-description');

                if ($description) {
                    $bill->set('description', $description);
                }

                $time = $this->params()->fromPost('ebf-' . $bbid . '-time');

                if ($time && is_numeric($time)) {
                    $bill->set('time', $time * 60);
                }

                $quantity = $this->params()->fromPost('ebf-' . $bbid . '-quantity');

                if ($quantity && is_numeric($quantity)) {
                    $bill->set('quantity', $quantity);
                }

                $price = $this->params()->fromPost('ebf-' . $bbid . '-price');

                if ($price && is_numeric($price)) {
                    $bill->set('price', $price);
                }

                $vat = $this->params()->fromPost('ebf-' . $bbid . '-vat');

                if ($vat) {
                    $vat_parts = explode(',', $vat);

                    if (count($vat_parts) == 2 && is_numeric($vat_parts[0]) && is_numeric($vat_parts[1])) {
                        $bill->set('rate', $vat_parts[0]);
                        $bill->set('gross', $vat_parts[1]);
                    }
                }

                if ($description) {
                    $bookingBillManager->save($bill);
                }
            }

            $save = $this->params()->fromPost('ebf-save');
            $saveAndBack = $this->params()->fromPost('ebf-save-and-back');

            $this->flashMessenger()->addSuccessMessage('Booking-Bill has been saved');

            if ($save) {
                return $this->redirect()->toRoute('backend/booking/bills', ['bid' => $bid]);
            } else if ($saveAndBack) {
                return $this->redirect()->toRoute('user/bookings/bills', ['bid' => $bid]);
            }
        }

        return array(
            'booking' => $booking,
            'bookingStatusService' => $bookingStatusService,
            'bills' => $bills,
            'user' => $user,
        );
    }

}
