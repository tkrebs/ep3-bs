<?php

namespace Backend\Controller;

use Backend\Form\Config\TextForm;
use Zend\Mvc\Controller\AbstractActionController;

class ConfigController extends AbstractActionController
{

    public function indexAction()
    {
        $this->authorize('admin.config');
    }

    public function textAction()
    {
        $this->authorize('admin.config');

        $serviceManager = @$this->getServiceLocator();
        $optionManager = $serviceManager->get('Base\Manager\OptionManager');
        $formElementManager = $serviceManager->get('FormElementManager');

        $textForm = $formElementManager->get('Backend\Form\Config\TextForm');

        if ($this->getRequest()->isPost()) {
            $textForm->setData($this->params()->fromPost());

            if ($textForm->isValid()) {
                $textData = $textForm->getData();

                foreach (TextForm::$definitions as $key => $value) {
                    $formKey = str_replace('.', '_', $key);

	                $currentValue = $optionManager->get($key);
                    $formValue = $textData['cf-' . $formKey];

	                if (isset($value[2]) && $value[2]) {
				        $type = $value[2];
			        } else {
				        $type = 'Text';
			        }

	                if ($type == 'Checkbox') {
				        $formValue = (boolean) $formValue;
			        }

                    if (($formValue && $formValue != $currentValue) || is_bool($formValue)) {
                        $optionManager->set($key, $formValue, $this->config('i18n.locale'));
                    }
                }

                $this->flashMessenger()->addSuccessMessage('Names and text have been saved');

                return $this->redirect()->toRoute('backend/config/text');
            }
        } else {
            foreach (TextForm::$definitions as $key => $value) {
                $formKey = str_replace('.', '_', $key);
                $textForm->get('cf-' . $formKey)->setValue($optionManager->get($key));
            }
        }

        return array(
            'textForm' => $textForm,
        );
    }

    public function infoAction()
    {
        $this->authorize('admin.config');

        if ($this->getRequest()->isPost()) {
            $info = $this->params()->fromPost('cf-info');

            if ($info && strlen($info) > 32) {
                $optionManager = @$this->getServiceLocator()->get('Base\Manager\OptionManager');
                $optionManager->set('subject.about', $info, $this->config('i18n.locale'));

                $this->flashMessenger()->addSuccessMessage('Info page has been saved');
            } else {
                $this->flashMessenger()->addErrorMessage('Info page text is too short');
            }

            return $this->redirect()->toRoute('backend/config/info');
        }
    }

    public function helpAction()
    {
        $this->authorize('admin.config');

        if ($this->getRequest()->isPost()) {
            $help = $this->params()->fromPost('cf-help');

            if ($help && strlen($help) > 32) {
                $optionManager = @$this->getServiceLocator()->get('Base\Manager\OptionManager');
                $optionManager->set('subject.help', $help, $this->config('i18n.locale'));

                $this->flashMessenger()->addSuccessMessage('Help page has been saved');
            } else {
                $this->flashMessenger()->addErrorMessage('Help page text is too short');
            }

            return $this->redirect()->toRoute('backend/config/help');
        }
    }

    public function behaviourAction()
    {
        $this->authorize('admin.config');

        $serviceManager = @$this->getServiceLocator();
        $optionManager = $serviceManager->get('Base\Manager\OptionManager');
        $formElementManager = $serviceManager->get('FormElementManager');

        $behaviourForm = $formElementManager->get('Backend\Form\Config\BehaviourForm');

        if ($this->getRequest()->isPost()) {
            $behaviourForm->setData($this->params()->fromPost());

            if ($behaviourForm->isValid()) {
                $data = $behaviourForm->getData();

                $maintenance = $data['cf-maintenance'];
                $maintenanceMessage = $data['cf-maintenance-message'];
                $registration = $data['cf-registration'];
                $registrationMessage = $data['cf-registration-message'];
                $activation = $data['cf-activation'];
                $calendarDays = $data['cf-calendar-days'];
                $calendarDayExceptions = $data['cf-calendar-day-exceptions'];

                $locale = $this->config('i18n.locale');

                $optionManager->set('service.maintenance', $maintenance);
                $optionManager->set('service.maintenance.message', $maintenanceMessage, $locale);
                $optionManager->set('service.user.registration', $registration);
                $optionManager->set('service.user.registration.message', $registrationMessage, $locale);
                $optionManager->set('service.user.activation', $activation);
                $optionManager->set('service.calendar.days', $calendarDays);
                $optionManager->set('service.calendar.day-exceptions', $calendarDayExceptions);

                $this->flashMessenger()->addSuccessMessage('Configuration has been saved');
            } else {
                $this->flashMessenger()->addErrorMessage('Configuration is (partially) invalid');
            }

            return $this->redirect()->toRoute('backend/config/behaviour');
        } else {
            $behaviourForm->get('cf-maintenance')->setValue($optionManager->get('service.maintenance', 'false'));
            $behaviourForm->get('cf-maintenance-message')->setValue($optionManager->get('service.maintenance.message'));
            $behaviourForm->get('cf-registration')->setValue($optionManager->get('service.user.registration', 'false'));
            $behaviourForm->get('cf-registration-message')->setValue($optionManager->get('service.user.registration.message'));
            $behaviourForm->get('cf-activation')->setValue($optionManager->get('service.user.activation', 'email'));
            $behaviourForm->get('cf-calendar-days')->setValue($optionManager->get('service.calendar.days', '4'));
            $behaviourForm->get('cf-calendar-day-exceptions')->setValue($optionManager->get('service.calendar.day-exceptions'));
        }

        return array(
            'behaviourForm' => $behaviourForm,
        );
    }

    public function behaviourRulesAction()
    {
        $this->authorize('admin.config');

        $serviceManager = @$this->getServiceLocator();
        $optionManager = $serviceManager->get('Base\Manager\OptionManager');
        $formElementManager = $serviceManager->get('FormElementManager');

        $rulesForm = $formElementManager->get('Backend\Form\Config\BehaviourRulesForm');

        $locale = $this->config('i18n.locale');

        if ($this->getRequest()->isGet()) {

            switch ($this->params()->fromQuery('delete')) {
                case 'terms':
                    $optionManager->set('service.user.registration.terms.file', null, $locale);

                    $this->flashMessenger()->addSuccessMessage('Configuration has been updated');

                    return $this->redirect()->toRoute('backend/config/behaviour/rules');
                case 'privacy':
                    $optionManager->set('service.user.registration.privacy.file', null, $locale);

                    $this->flashMessenger()->addSuccessMessage('Configuration has been updated');

                    return $this->redirect()->toRoute('backend/config/behaviour/rules');
            }
        }

        if ($this->getRequest()->isPost()) {
            $post = array_merge_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );

            $rulesForm->setData($post);

            if ($rulesForm->isValid()) {
                $rulesData = $rulesForm->getData();

                /* Save business terms */

                $termsFile = $rulesData['cf-terms-file'];

                if (isset($termsFile['name']) && $termsFile['name'] && isset($termsFile['tmp_name']) && $termsFile['tmp_name']) {
                    $rulesFileName = $termsFile['name'];
                    $rulesFileName = str_replace('.pdf', '', $rulesFileName);
                    $rulesFileName = trim($rulesFileName);
                    $rulesFileName = preg_replace('/[^a-zA-Z0-9 -]/', '', $rulesFileName);
                    $rulesFileName = str_replace(' ', '-', $rulesFileName);
                    $rulesFileName = strtolower($rulesFileName);

                    $destination = sprintf('docs-client/upload/%s.pdf',
                        $rulesFileName);

                    move_uploaded_file($termsFile['tmp_name'], sprintf('%s/public/%s', getcwd(), $destination));

                    $optionManager->set('service.user.registration.terms.file', $destination, $locale);
                }

                $optionManager->set('service.user.registration.terms.name', $rulesData['cf-terms-name'], $locale);

                /* Save privacy policy */

                $privacyFile = $rulesData['cf-privacy-file'];

                if (isset($privacyFile['name']) && $privacyFile['name'] && isset($privacyFile['tmp_name']) && $privacyFile['tmp_name']) {
                    $privacyFileName = $privacyFile['name'];
                    $privacyFileName = str_replace('.pdf', '', $privacyFileName);
                    $privacyFileName = trim($privacyFileName);
                    $privacyFileName = preg_replace('/[^a-zA-Z0-9 -]/', '', $privacyFileName);
                    $privacyFileName = str_replace(' ', '-', $privacyFileName);
                    $privacyFileName = strtolower($privacyFileName);

                    $destination = sprintf('docs-client/upload/%s.pdf',
                        $privacyFileName);

                    move_uploaded_file($privacyFile['tmp_name'], sprintf('%s/public/%s', getcwd(), $destination));

                    $optionManager->set('service.user.registration.privacy.file', $destination, $locale);
                }

                $optionManager->set('service.user.registration.privacy.name', $rulesData['cf-privacy-name'], $locale);

                $this->flashMessenger()->addSuccessMessage('Configuration has been saved');

                return $this->redirect()->toRoute('backend/config/behaviour/rules');
            }
        } else {
            $rulesForm->setData(array(
                'cf-terms-name' => $optionManager->get('service.user.registration.terms.name'),
                'cf-privacy-name' => $optionManager->get('service.user.registration.privacy.name'),
            ));
        }

        return array(
            'rulesForm' => $rulesForm,
        );
    }

    public function behaviourStatusColorsAction()
    {
        $this->authorize('admin.config');

        $serviceManager = @$this->getServiceLocator();
        $formElementManager = $serviceManager->get('FormElementManager');

        $statusColorsForm = $formElementManager->get('Backend\Form\Config\BehaviourStatusColorsForm');

        $bookingStatusService = $serviceManager->get('Booking\Service\BookingStatusService');

        if ($this->getRequest()->isPost()) {
            $statusColorsForm->setData($this->params()->fromPost());

            if ($statusColorsForm->isValid()) {
                $data = $statusColorsForm->getData();

                $statusColors = $data['cf-status-colors'];

                if ($bookingStatusService->checkStatusColors($statusColors)) {
                    $bookingStatusService->setStatusColors($statusColors, $this->config('i18n.locale'));

                    $this->flashMessenger()->addSuccessMessage('Configuration has been saved');
                } else {
                    $this->flashMessenger()->addErrorMessage('Configuration is (partially) invalid');
                }
            } else {
                $this->flashMessenger()->addErrorMessage('Configuration is (partially) invalid');
            }

            return $this->redirect()->toRoute('backend/config/behaviour/status-colors');
        } else {
            $statusColorsForm->get('cf-status-colors')->setValue($bookingStatusService->getStatusColorsRaw());
        }

        return array(
            'statusColorsForm' => $statusColorsForm,
        );
    }

}
