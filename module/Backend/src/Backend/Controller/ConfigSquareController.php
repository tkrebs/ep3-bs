<?php

namespace Backend\Controller;

use Square\Entity\Square;
use Square\Entity\SquareProduct;
use Zend\Mvc\Controller\AbstractActionController;

class ConfigSquareController extends AbstractActionController
{

    public function indexAction()
    {
        $this->authorize('admin.config');

        $squareManager = $this->getServiceLocator()->get('Square\Manager\SquareManager');
        $squares = $squareManager->getAll();

        return array(
            'squares' => $squares,
        );
    }

    public function editAction()
    {
        $this->authorize('admin.config');

        $serviceManager = $this->getServiceLocator();
        $squareManager = $serviceManager->get('Square\Manager\SquareManager');
        $formElementManager = $serviceManager->get('FormElementManager');

	    $locale = $this->config('i18n.locale');

        $sid = $this->params()->fromRoute('sid');

        if ($sid) {
            $square = $squareManager->get($sid);
        } else {
            $square = null;
        }

        $editForm = $formElementManager->get('Backend\Form\ConfigSquare\EditForm');

        if ($this->getRequest()->isPost()) {
            $editForm->setData($this->params()->fromPost());

            if ($editForm->isValid()) {
                $editData = $editForm->getData();

                if (! $square) {
                    $square = new Square();
                }

                $square->set('name', $editData['cf-name']);
                $square->set('status', $editData['cf-status']);
                $square->setMeta('readonly.message', $editData['cf-readonly-message']);
                $square->set('priority', $editData['cf-priority']);
                $square->set('capacity', $editData['cf-capacity']);
                $square->setMeta('capacity-ask-names', $editData['cf-capacity-ask-names']);
                $square->set('capacity_heterogenic', $editData['cf-capacity-heterogenic']);
                $square->setMeta('private_names', $editData['cf-name-visibility'] == 'private' ? 'true' : 'false');
                $square->setMeta('public_names', $editData['cf-name-visibility'] == 'public' ? 'true' : 'false');
                $square->set('time_start', $editData['cf-time-start']);
                $square->set('time_end', $editData['cf-time-end']);
                $square->set('time_block', max($editData['cf-time-block'], 10) * 60);
                $square->set('time_block_bookable', max($editData['cf-time-block-bookable'], 10) * 60);
                $square->setMeta('pseudo-time-block-bookable', $editData['cf-pseudo-time-block-bookable'] ? 'true' : 'false');
                $square->set('time_block_bookable_max', max($editData['cf-time-block-bookable-max'], 10) * 60);
                $square->set('min_range_book', (float) $editData['cf-min-range-book'] * 60);
                $square->set('range_book', (float) $editData['cf-range-book'] * 60 * 60 * 24);
                $square->set('max_active_bookings', $editData['cf-max-active-bookings']);
                $square->set('range_cancel', $editData['cf-range-cancel'] * 60 * 60);
	            $square->setMeta('label.free', $editData['cf-label-free'], $locale);

                $squareManager->save($square);

                $this->flashMessenger()->addSuccessMessage('Square has been saved');

                return $this->redirect()->toRoute('backend/config/square');
            }
        } else {
            if ($square) {
                $private_names = $square->getMeta('private_names', 'false');
                $public_names = $square->getMeta('public_names', 'false');

                if ($private_names == 'true') {
                    $nameVisibility = 'private';
                } else if ($public_names == 'true') {
                    $nameVisibility = 'public';
                } else {
                    $nameVisibility = null;
                }

                $editForm->setData(array(
                    'cf-name' => $square->get('name'),
                    'cf-status' => $square->get('status'),
                    'cf-readonly-message' => $square->getMeta('readonly.message'),
                    'cf-priority' => $square->get('priority'),
                    'cf-capacity' => $square->get('capacity'),
                    'cf-capacity-ask-names' => $square->getMeta('capacity-ask-names'),
                    'cf-capacity-heterogenic' => $square->get('capacity_heterogenic'),
                    'cf-name-visibility' => $nameVisibility,
                    'cf-time-start' => substr($square->get('time_start'), 0, 5),
                    'cf-time-end' => substr($square->get('time_end'), 0, 5),
                    'cf-time-block' => round($square->get('time_block') / 60),
                    'cf-time-block-bookable' => round($square->get('time_block_bookable') / 60),
                    'cf-pseudo-time-block-bookable' => $square->getMeta('pseudo-time-block-bookable', 'false') == 'true',
                    'cf-time-block-bookable-max' => round($square->get('time_block_bookable_max') / 60),
                    'cf-min-range-book' => round($square->get('min_range_book') / 60),
                    'cf-range-book' => round($square->get('range_book') / 60 / 60 / 24),
                    'cf-max-active-bookings' => $square->get('max_active_bookings'),
                    'cf-range-cancel' => round($square->get('range_cancel') / 60 / 60, 2),
	                'cf-label-free' => $square->getMeta('label.free'),
                ));
            } else {
                $editForm->setData(array(
                    'cf-status' => 'enabled',
                    'cf-priority' => 1,
                    'cf-capacity' => 1,
                    'cf-capacity-heterogenic' => false,
                    'cf-time-start' => '08:00',
                    'cf-time-end' => '23:00',
                    'cf-time-block' => 60,
                    'cf-time-block-bookable' => 30,
                    'cf-pseudo-time-block-bookable' => false,
                    'cf-time-block-bookable-max' => 180,
                    'cf-min-range-book' => 0,
                    'cf-range-book' => 56,
                    'cf-max-active-bookings' => 0,
                    'cf-range-cancel' => 24,
                ));
            }
        }

        return array(
            'square' => $square,
            'editForm' => $editForm,
        );
    }

    public function editInfoAction()
    {
        $this->authorize('admin.config');

        $serviceManager = $this->getServiceLocator();
        $squareManager = $serviceManager->get('Square\Manager\SquareManager');
        $formElementManager = $serviceManager->get('FormElementManager');

        $sid = $this->params()->fromRoute('sid');

        $square = $squareManager->get($sid);

        $editForm = $formElementManager->get('Backend\Form\ConfigSquare\EditInfoForm');

        if ($this->getRequest()->isPost()) {
            $post = array_merge_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );

            $editForm->setData($post);

            if ($editForm->isValid()) {
                $editData = $editForm->getData();

                $locale = $this->config('i18n.locale');

                $square->setMeta('info.pre', $editData['cf-info-pre'], $locale);
                $square->setMeta('info.post', $editData['cf-info-post'], $locale);
                $square->setMeta('rules.text', $editData['cf-rules-text'], $locale);

                $documentFile = $editData['cf-rules-document-file'];

                if (isset($documentFile['name']) && $documentFile['name'] && isset($documentFile['tmp_name']) && $documentFile['tmp_name']) {
                    $documentFileName = $documentFile['name'];
                    $documentFileName = str_replace('.pdf', '', $documentFileName);
                    $documentFileName = trim($documentFileName);
                    $documentFileName = preg_replace('/[^a-zA-Z0-9 -]/', '', $documentFileName);
                    $documentFileName = str_replace(' ', '-', $documentFileName);
                    $documentFileName = strtolower($documentFileName);

                    $destination = sprintf('docs-client/upload/%s.pdf',
                        $documentFileName);

                    move_uploaded_file($documentFile['tmp_name'], sprintf('%s/public/%s', getcwd(), $destination));

                    $square->setMeta('rules.document.file', $destination, $locale);
                }

                $square->setMeta('rules.document.name', $editData['cf-rules-document-name'], $locale);

                $squareManager->save($square);

                $this->flashMessenger()->addSuccessMessage('Square has been saved');

                return $this->redirect()->toRoute('backend/config/square');
            }
        } else {
            $editForm->setData(array(
                'cf-info-pre' => $square->getMeta('info.pre'),
                'cf-info-post' => $square->getMeta('info.post'),
                'cf-rules-text' => $square->getMeta('rules.text'),
                'cf-rules-document-name' => $square->getMeta('rules.document.name'),
            ));
        }

        return array(
            'square' => $square,
            'editForm' => $editForm,
        );
    }

    public function pricingAction()
    {
        $this->authorize('admin.config');

        $serviceManager = $this->getServiceLocator();
        $optionManager = $serviceManager->get('Base\Manager\OptionManager');
        $squareManager = $serviceManager->get('Square\Manager\SquareManager');
        $squarePricingManager = $serviceManager->get('Square\Manager\SquarePricingManager');

        $squares = $squareManager->getAll();
        $squaresTimeBlock = $squareManager->getMinTimeBlock();
        $squaresPricingRules = $squarePricingManager->getAll();

        if ($this->getRequest()->isPost()) {
            $rulesCount = $this->params()->fromPost('pricing-rules-count');

            if (is_numeric($rulesCount) && $rulesCount > 0) {

                try {

                    $rules = array();

                    for ($i = 0; $i < $rulesCount; $i++) {
                        $rule = $this->params()->fromPost('pricing-rule-' . $i);
                        $rule = urldecode($rule);
                        $rule = json_decode($rule);

                        // Transform sid if null
                        if ($rule[0] == 'null') {
                            $rule[0] = null;
                        }

                        // Transform dates
                        $rule[2] = implode('-', array_reverse(explode('.', $rule[2])));
                        $rule[3] = implode('-', array_reverse(explode('.', $rule[3])));

                        // Transform price to cents by removing the comma
                        $rule[8] = str_replace(',', '', $rule[8]);

                        // Transform time block from minutes to seconds
                        $rule[11] *= 60;

                        $rules[] = $rule;
                    }

                    $squarePricingManager->create($rules);

                    $this->flashMessenger()->addMessage('Pricing rules have been saved');
                } catch (\Exception $e) {
                    $this->flashMessenger()->addErrorMessage($e->getMessage());
                }
            } else {
                $this->flashMessenger()->addErrorMessage('Unknown pricing rules error');
            }

            // Set pricing visibility
            $pricingVisibility = $this->params()->fromPost('pricing-visibility', 'never');
            $optionManager->set('service.pricing.visibility', $pricingVisibility);

            return $this->redirect()->toRoute('backend/config/square/pricing');
        }

        return array(
            'squares' => $squares,
            'squaresTimeBlock' => $squaresTimeBlock,
            'squaresPricingRules' => $squaresPricingRules,
        );
    }

    public function productAction()
    {
        $this->authorize('admin.config');

        $squareProductManager = $this->getServiceLocator()->get('Square\Manager\SquareProductManager');
        $squareProducts = $squareProductManager->getAll('priority ASC');

        return array(
            'squareProducts' => $squareProducts,
        );
    }

    public function productEditAction()
    {
        $this->authorize('admin.config');

        $serviceManager = $this->getServiceLocator();
        $squareProductManager = $serviceManager->get('Square\Manager\SquareProductManager');
        $formElementManager = $serviceManager->get('FormElementManager');

        $spid = $this->params()->fromRoute('spid');

        if ($spid) {
            $squareProduct = $squareProductManager->get($spid);
        } else {
            $squareProduct = null;
        }

        $editForm = $formElementManager->get('Backend\Form\ConfigSquare\EditProductForm');

        if ($this->getRequest()->isPost()) {
            $editForm->setData($this->params()->fromPost());

            if ($editForm->isValid()) {
                $editData = $editForm->getData();

                if (! $squareProduct) {
                    $squareProduct = new SquareProduct();
                }

                $sid = $editData['cf-square'];

                if ($sid == 'null') {
                    $sid = null;
                }

                $dateStart = $editData['cf-date-start'];

                if ($dateStart) {
                    $dateStart = (new \DateTime($dateStart))->format('Y-m-d');
                } else {
                    $dateStart = null;
                }

                $dateEnd = $editData['cf-date-end'];

                if ($dateEnd) {
                    $dateEnd = (new \DateTime($dateEnd))->format('Y-m-d');
                } else {
                    $dateEnd = null;
                }

                $price = str_replace(',', '.', $editData['cf-price']);
                $price = floatval($price);
                $price *= 100;

                $locale = $editData['cf-locale'];

                if ($locale == '0') {
                    $locale = null;
                }

                $squareProduct->set('name', $editData['cf-name']);
                $squareProduct->set('description', $editData['cf-description']);
                $squareProduct->set('options', $editData['cf-options']);
                $squareProduct->set('sid', $sid);
                $squareProduct->set('priority', $editData['cf-priority']);
                $squareProduct->set('date_start', $dateStart);
                $squareProduct->set('date_end', $dateEnd);
                $squareProduct->set('price', $price);
                $squareProduct->set('gross', $editData['cf-gross']);
                $squareProduct->set('rate', $editData['cf-rate']);
                $squareProduct->set('locale', $locale);

                $squareProductManager->save($squareProduct);

                $this->flashMessenger()->addSuccessMessage('Product has been saved');

                return $this->redirect()->toRoute('backend/config/square/product');
            }
        } else {
            if ($squareProduct) {
                $editForm->setData(array(
                    'cf-name' => $squareProduct->get('name'),
                    'cf-description' => $squareProduct->get('description'),
                    'cf-options' => $squareProduct->get('options'),
                    'cf-square' => $squareProduct->get('sid'),
                    'cf-priority' => $squareProduct->get('priority'),
                    'cf-date-start' => $this->dateFormat($squareProduct->get('date_start')),
                    'cf-date-end' => $this->dateFormat($squareProduct->get('date_end')),
                    'cf-price' => $this->numberFormat($squareProduct->get('price') / 100),
                    'cf-gross' => $squareProduct->get('gross'),
                    'cf-rate' => $squareProduct->get('rate'),
                ));
            }
        }

        return array(
            'squareProduct' => $squareProduct,
            'editForm' => $editForm,
        );
    }

    public function productDeleteAction()
    {
        $this->authorize('admin.config');

        $spid = $this->params()->fromRoute('spid');

        $serviceManager = $this->getServiceLocator();
        $squareProductManager = $serviceManager->get('Square\Manager\SquareProductManager');

        $squareProduct = $squareProductManager->get($spid);

        if ($this->params()->fromQuery('confirmed') == 'true') {

            $squareProductManager->delete($squareProduct);

            $this->flashMessenger()->addSuccessMessage('Product has been deleted');

            return $this->redirect()->toRoute('backend/config/square/product');
        }

        return array(
            'spid' => $spid,
        );
    }

    public function couponAction()
    {
        $this->authorize('admin.config');
    }

    public function deleteAction()
    {
        $this->authorize('admin.config');

        $sid = $this->params()->fromRoute('sid');

        $serviceManager = $this->getServiceLocator();
        $bookingManager = $serviceManager->get('Booking\Manager\BookingManager');
        $squareManager = $serviceManager->get('Square\Manager\SquareManager');

        $square = $squareManager->get($sid);
        $squareBookings = $bookingManager->getBy(['sid' => $sid]);

        if ($this->params()->fromQuery('confirmed') == 'true') {

            if ($squareBookings) {

                // There are already bookings for this square, so we can only set its status to disabled
                $square->set('status', 'disabled');
                $squareManager->save($square);

                $this->flashMessenger()->addSuccessMessage('Square status has been set to deleted');
            } else {

                // There are no bookings, so we can actually delete it
                $squareManager->delete($square);

                $this->flashMessenger()->addSuccessMessage('Square has been deleted');
            }

            return $this->redirect()->toRoute('backend/config/square');
        }

        return array(
            'sid' => $sid,
            'squareBookings' => $squareBookings,
        );
    }

}
