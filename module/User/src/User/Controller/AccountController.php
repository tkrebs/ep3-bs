<?php

namespace User\Controller;

use DateTime;
use RuntimeException;
use Zend\Crypt\Password\Bcrypt;
use Zend\Mvc\Controller\AbstractActionController;

class AccountController extends AbstractActionController
{

    public function passwordAction()
    {
        $serviceManager = @$this->getServiceLocator();
        $formElementManager = $serviceManager->get('FormElementManager');

        $passwordForm = $formElementManager->get('User\Form\PasswordForm');
        $passwordMessage = null;

        if ($this->getRequest()->isPost()) {
            $passwordForm->setData($this->params()->fromPost());

            if ($passwordForm->isValid()) {
                $passwordData = $passwordForm->getData();

                $userManager = $serviceManager->get('User\Manager\UserManager');
                $user = current( $userManager->getBy(array('email' => $passwordData['pf-email'])) );

                if ($user) {
                    $mailMessage = $this->t('We have just received your request to reset your password.') . "\r\n\r\n";

                    switch ($user->need('status')) {
                        case 'placeholder':
                            $mailMessage .= $this->t('Unfortunately, your account is considered a placeholder and thus cannot login.');
                            break;
                        case 'blocked':
                            $mailMessage .= $this->t('Unfortunately, your account is currently blocked. Please contact us for support.');
                            break;
                        case 'disabled':
                            $mailMessage .= $this->t('Unfortunately, your account has not yet been activated. If you did not receive an activation email yet, you can request a new one here:') . "\r\n\r\n";
                            $mailMessage .= $this->url()->fromRoute('user/activation-resend', [], ['force_canonical' => true]);

                            break;
                        case 'enabled':
                            $resetCode = base64_encode( substr($user->need('pw'), 16, 8) );

                            $mailMessage .= $this->t('Simply visit the following website to type your new password:') . "\r\n\r\n";
                            $mailMessage .= $this->url()->fromRoute('user/password-reset', [], ['query' => ['id' => $user->need('uid'), 'code' => $resetCode], 'force_canonical' => true]);

                            break;
                        case 'assist':
                        case 'admin':
                            $mailMessage .= $this->t('However, you are using a privileged account. For safety, you cannot reset your password this way. Please contact the system support.');
                            break;
                        default:
                            $mailMessage .= $this->t('Unfortunately, your account seems somewhat unique, thus we are unsure how to treat it. Mind contacting us?');
                            break;
                    }

                    $userMailService = $serviceManager->get('User\Service\MailService');
                    $userMailService->send($user, $this->t('Forgot your password?'), $mailMessage);
                }
            }

            $passwordForm->get('pf-email')->setValue('');

            $passwordMessage = sprintf('%s <div class="small-text">(%s)</div>',
                $this->t('All right, you should receive an email from us soon'),
                $this->t('if we find a valid user account with this email address'));
        }

        return array(
            'passwordForm' => $passwordForm,
            'passwordMessage' => $passwordMessage,
        );
    }

    public function passwordResetAction()
    {
        $resetUid = $this->params()->fromQuery('id');
        $resetCode = $this->params()->fromQuery('code');

        if (! (is_numeric($resetUid) && $resetUid > 0 && preg_match('/^[a-zA-Z0-9\+\/\=]+$/', $resetCode))) {
            throw new RuntimeException('Your token to reset your password is invalid or expired. Please request a new email.');
        }

        $serviceManager = @$this->getServiceLocator();

        $userManager = $serviceManager->get('User\Manager\UserManager');
        $user = $userManager->get($resetUid, false);

        if (! $user) {
            throw new RuntimeException('Your token to reset your password is invalid or expired. Please request a new email.');
        }

        $actualResetCode = base64_encode( substr($user->need('pw'), 16, 8) );

        if ($resetCode != $actualResetCode) {
            throw new RuntimeException('Your token to reset your password is invalid or expired. Please request a new email.');
        }

        $formElementManager = $serviceManager->get('FormElementManager');

        $resetForm = $formElementManager->get('User\Form\PasswordResetForm');
        $resetMessage = null;

        if ($this->getRequest()->isPost()) {
            $resetForm->setData($this->params()->fromPost());

            if ($resetForm->isValid()) {
                $resetData = $resetForm->getData();

                $bcrypt = new Bcrypt();
                $bcrypt->setCost(6);

                $user->set('pw', $bcrypt->create($resetData['prf-pw1']));

                $user->set('last_activity', date('Y-m-d H:i:s'));
                $user->set('last_ip', $_SERVER['REMOTE_ADDR']);

                $userManager->save($user);

                $resetMessage = 'All right, your password has been changed. You can now log into your account.';
            }
        }

        return array(
            'resetUid' => $resetUid,
            'resetCode' => $resetCode,
            'resetForm' => $resetForm,
            'resetMessage' => $resetMessage,
        );
    }

    public function registrationAction()
    {
        $serviceManager = @$this->getServiceLocator();

        $formElementManager = $serviceManager->get('FormElementManager');

        $registrationForm = $formElementManager->get('User\Form\RegistrationForm');

        if ($this->getRequest()->isPost() && $this->option('service.user.registration') == 'true') {
            $registrationForm->setData($this->params()->fromPost());

            if ($registrationForm->isValid()) {
                $registrationData = $registrationForm->getData();

                $meta = array();
                $meta['gender'] = $registrationData['rf-gender'];

                if (isset($registrationData['rf-lastname']) && $registrationData['rf-lastname']) {
                    $meta['firstname'] = ucfirst($registrationData['rf-firstname']);
                    $meta['lastname'] = ucfirst($registrationData['rf-lastname']);

                    $alias = $meta['firstname'] . ' ' . $meta['lastname'];
                } else {
                    $meta['name'] = $registrationData['rf-firstname'];

                    if ($meta['gender'] == 'male' || $meta['gender'] == 'female' || $meta['gender'] == 'family') {
                        $meta['name'] = ucfirst($meta['name']);
                    }

                    $alias = $meta['name'];
                }

                $meta['street'] = $registrationData['rf-street'] . ' ' . $registrationData['rf-number'];
                $meta['zip'] = $registrationData['rf-zip'];
                $meta['city'] = $registrationData['rf-city'];
                $meta['phone'] = $registrationData['rf-phone'];

                if (! (isset($registrationData['rf-birthdate']) && preg_match('/^([ \,\-\.0-9\x{00c0}-\x{01ff}a-zA-Z]){4,}$/u', $registrationData['rf-birthdate']))) {
                    $registrationData['rf-birthdate'] = null;
                }

                if (isset($registrationData['rf-birthdate']) && $registrationData['rf-birthdate']) {
                    $meta['birthdate'] = $registrationData['rf-birthdate'];
                }

                $meta['locale'] = $this->config('i18n.locale');

                if ($this->option('service.user.activation') == 'immediate') {
                    $status = 'enabled';
                } else {
                    $status = 'disabled';
                }

                $userManager = $serviceManager->get('User\Manager\UserManager');

                $user = $userManager->create($alias, $status, $registrationData['rf-email1'], $registrationData['rf-pw1'], $meta);
                $user->set('last_ip', $_SERVER['REMOTE_ADDR']);

                $userManager->save($user);

                /* Send confirmation email to administration for manual activation */

                if ($this->option('service.user.activation') == 'manual-email') {
                    $backendMailService = $serviceManager->get('Backend\Service\MailService');
                    $backendMailService->send(
                        $this->t('New registration waiting for activation'),
                        sprintf($this->t('A new user has registered to your %s. According to your configuration, this user will not be able to book %s until you manually activate him.'),
                            $this->option('service.name.full', false), $this->option('subject.square.type.plural', false)));
                }

                /* Send confirmation email to user for activation */

                if ($this->option('service.user.activation') == 'email') {

                    /* Activation code is "created" hash */

                    $activationCode = urlencode( sha1($user->need('created')) );
                    $activationLink = $this->url()->fromRoute('user/activation', [], ['query' => ['id' => $user->need('uid'), 'code' => $activationCode], 'force_canonical' => true]);

                    $subject = sprintf($this->t('Your registration to the %s %s'),
                        $this->option('client.name.short', false), $this->option('service.name.full', false));

                    $text = sprintf($this->t("welcome to the %s %s!\r\n\r\nThank you for your registration to our service.\r\n\r\nBefore you can completely use your new user account to book spare %s online, you have to activate it by simply clicking the following link. That's all!\r\n\r\n%s"),
                        $this->option('client.name.full', false), $this->option('service.name.full', false), $this->option('subject.square.type.plural', false), $activationLink);

                    $userMailService = $serviceManager->get('User\Service\MailService');
                    $userMailService->send($user, $subject, $text);
                }

                return $this->redirect()->toRoute('user/registration-confirmation');
            }
        }

        return array(
            'registrationForm' => $registrationForm,
        );
    }

    public function registrationConfirmationAction()
    {
        return array(
            'activation' => $this->option('service.user.activation', false),
        );
    }

    public function activationAction()
    {
        $activationUid = $this->params()->fromQuery('id');
        $activationCode = urldecode($this->params()->fromQuery('code'));

        if (! (is_numeric($activationUid) && $activationUid > 0)) {
            throw new RuntimeException('Your activation code seems invalid. Please try again.');
        }

        $userManager = @$this->getServiceLocator()->get('User\Manager\UserManager');
        $user = $userManager->get($activationUid, false);

        if (! $user) {
            throw new RuntimeException('Your activation code seems invalid. Please try again.');
        }

        $actualActivationCode = sha1($user->need('created'));

        if ($activationCode != $actualActivationCode) {
            throw new RuntimeException('Your activation code seems invalid. Please try again.');
        }

        $user->set('status', $user->getMeta('status_before_reactivation', 'enabled'));
        $user->set('last_activity', date('Y-m-d H:i:s'));
        $user->set('last_ip', $_SERVER['REMOTE_ADDR']);

        $userManager->save($user);
    }

    public function activationResendAction()
    {
        if ($this->option('service.user.activation') != 'email') {
            throw new RuntimeException('You cannot manually activate your account currently');
        }

        $serviceManager = @$this->getServiceLocator();

        $formElementManager = $serviceManager->get('FormElementManager');

        $activationResendForm = $formElementManager->get('User\Form\ActivationResendForm');
        $activationResendMessage = null;

        if ($this->getRequest()->isPost()) {
            $activationResendForm->setData($this->params()->fromPost());

            if ($activationResendForm->isValid()) {
                $activationResendData = $activationResendForm->getData();

                $userManager = $serviceManager->get('User\Manager\UserManager');
                $user = current( $userManager->getBy(array('email' => $activationResendData['arf-email'])) );

                if ($user) {
                    $mailMessage = $this->t('We have just received your request for a new user account activation email.') . "\r\n\r\n";

                    switch ($user->need('status')) {
                        case 'placeholder':
                            $mailMessage .= $this->t('Unfortunately, your account is considered a placeholder and thus cannot be activated.');
                            break;
                        case 'blocked':
                            $mailMessage .= $this->t('Unfortunately, your account is currently blocked. Please contact us for support.');
                            break;
                        case 'disabled':

                            /* Activation code is "created" hash */

                            $activationCode = urlencode( sha1($user->need('created')) );
                            $activationLink = $this->url()->fromRoute('user/activation', [], ['query' => ['id' => $user->need('uid'), 'code' => $activationCode], 'force_canonical' => true]);

                            $mailMessage .= sprintf($this->t("Before you can completely use your new user account to book spare %s online, you have to activate it by simply clicking the following link. That's all!\r\n\r\n%s"),
                                $this->option('subject.square.type.plural', false), $activationLink);

                            break;
                        case 'enabled':
                        case 'assist':
                        case 'admin':
                            $mailMessage .= $this->t('However, your account has already been activated. You can login whenever you like!');
                            break;
                        default:
                            $mailMessage .= $this->t('Unfortunately, your account seems somewhat unique, thus we are unsure how to treat it. Mind contacting us?');
                            break;
                    }

                    $userMailService = $serviceManager->get('User\Service\MailService');
                    $userMailService->send($user, $this->t('User account activation'), $mailMessage);
                }
            }

            $activationResendForm->get('arf-email')->setValue('');

            $activationResendMessage = sprintf('%s <div class="small-text">(%s)</div>',
                $this->t('All right, you should receive an email from us soon'),
                $this->t('if we find a valid user account with this email address'));
        }

        return array(
            'activationResendForm' => $activationResendForm,
            'activationResendMessage' => $activationResendMessage,
        );
    }

    public function bookingsAction()
    {
        $serviceManager = @$this->getServiceLocator();

        $bookingManager = $serviceManager->get('Booking\Manager\BookingManager');
        $bookingBillManager = $serviceManager->get('Booking\Manager\Booking\BillManager');
        $reservationManager = $serviceManager->get('Booking\Manager\ReservationManager');
        $squareManager = $serviceManager->get('Square\Manager\SquareManager');
        $squareValidator = $serviceManager->get('Square\Service\SquareValidator');
        $userSessionManager = $serviceManager->get('User\Manager\UserSessionManager');

        $user = $userSessionManager->getSessionUser();

        if (! $user) {
            $this->redirectBack()->setOrigin('user/bookings');

            return $this->redirect()->toRoute('user/login');
        }

        $bookings = $bookingManager->getByValidity(array('uid' => $user->need('uid')));
        $reservations = $reservationManager->getByBookings($bookings, 'date DESC, time_start DESC');

        $bookingBillManager->getByBookings($bookings);

        return array(
            'now' => new DateTime(),
            'bookings' => $bookings,
            'reservations' => $reservations,
            'squareManager' => $squareManager,
            'squareValidator' => $squareValidator,
        );
    }

    public function billsAction()
    {
        $bid = $this->params()->fromRoute('bid');

        $serviceManager = @$this->getServiceLocator();

        $userSessionManager = $serviceManager->get('User\Manager\UserSessionManager');
        $user = $userSessionManager->getSessionUser();

        if (! $user) {
            $this->redirectBack()->setOrigin('user/bookings/bills', ['bid' => $bid]);

            return $this->redirect()->toRoute('user/login');
        }

        $bookingManager = $serviceManager->get('Booking\Manager\BookingManager');
        $bookingBillManager = $serviceManager->get('Booking\Manager\Booking\BillManager');
        $bookingStatusService = $serviceManager->get('Booking\Service\BookingStatusService');

        $booking = $bookingManager->get($bid);
        $bookingBillingStatus = $bookingStatusService->getStatusTitle($booking->getBillingStatus());

        if ($booking->get('uid') != $user->get('uid')) {
            if (! $user->can('admin.booking')) {
                throw new RuntimeException('You have no permission for this');
            }
        }

        $bills = $bookingBillManager->getBy(array('bid' => $bid), 'bbid ASC');

        return array(
            'booking' => $booking,
            'bookingBillingStatus' => $bookingBillingStatus,
            'bills' => $bills,
            'user' => $user,
        );
    }

    public function settingsAction()
    {
        $serviceManager = @$this->getServiceLocator();

        $userManager = $serviceManager->get('User\Manager\UserManager');
        $userSessionManager = $serviceManager->get('User\Manager\UserSessionManager');
        $formElementManager = $serviceManager->get('FormElementManager');

        $user = $userSessionManager->getSessionUser();

        if (! $user) {
            $this->redirectBack()->setOrigin('user/settings');

            return $this->redirect()->toRoute('user/login');
        }

        $editParam = $this->params()->fromQuery('edit');

        /* Phone form */

        $editPhoneForm = $formElementManager->get('User\Form\EditPhoneForm');

        if ($this->getRequest()->isPost() && $editParam == 'phone') {
            $editPhoneForm->setData($this->params()->fromPost());

            if ($editPhoneForm->isValid()) {
                $data = $editPhoneForm->getData();

                $phone = $data['epf-phone'];

                $user->setMeta('phone', $phone);
                $userManager->save($user);

                $this->flashMessenger()->addSuccessMessage(sprintf($this->t('Your %sphone number%s has been updated'),
                    '<b>', '</b>'));

                return $this->redirect()->toRoute('user/settings');
            }
        } else {
            $editPhoneForm->get('epf-phone')->setValue($user->getMeta('phone'));
        }

        /* Email form */

        $editEmailForm = $formElementManager->get('User\Form\EditEmailForm');

        if ($this->getRequest()->isPost() && $editParam == 'email') {
            $editEmailForm->setData($this->params()->fromPost());

            if ($editEmailForm->isValid()) {
                $data = $editEmailForm->getData();

                $email = $data['eef-email1'];

                $user->set('email', $email);

                if ($this->option('service.user.activation') == 'email') {

                    $user->setMeta('status_before_reactivation',
                        $user->get('status'));

                    $user->set('status', 'disabled');

                    /* Activation code is "created" hash */

                    $activationCode = urlencode( sha1($user->need('created')) );
                    $activationLink = $this->url()->fromRoute('user/activation', [], ['query' => ['id' => $user->need('uid'), 'code' => $activationCode], 'force_canonical' => true]);

                    $subject = sprintf($this->t('New email address at %s %s'),
                        $this->option('client.name.short', false), $this->option('service.name.full', false));

                    $text = sprintf($this->t("You have just changed your account's email address to this one.\r\n\r\nBefore you can completely use your new email address to book spare %s online again, you have to activate it by simply clicking the following link. That's all!\r\n\r\n%s"),
                        $this->option('subject.square.type.plural', false), $activationLink);

                    $userMailService = $serviceManager->get('User\Service\MailService');
                    $userMailService->send($user, $subject, $text);
                }

                $userManager->save($user);

                $this->flashMessenger()->addSuccessMessage(sprintf($this->t('Your %semail address%s has been updated'),
                    '<b>', '</b>'));

                return $this->redirect()->toRoute('user/settings');
            }
        } else {
            $editEmailForm->get('eef-email1')->setValue($user->get('email'));
            $editEmailForm->get('eef-email2')->setValue($user->get('email'));
        }

        /* Notifications form */

        $editNotificationsForm = $formElementManager->get('User\Form\EditNotificationsForm');

        if ($this->getRequest()->isPost() && $editParam == 'notifications') {
            $editNotificationsForm->setData($this->params()->fromPost());

            if ($editNotificationsForm->isValid()) {
                $data = $editNotificationsForm->getData();

                $bookingNotifications = $data['enf-booking-notifications'];

                $user->setMeta('notification.bookings', $bookingNotifications);

                $userManager->save($user);

                $this->flashMessenger()->addSuccessMessage(sprintf($this->t('Your %snotification settings%s have been updated'),
                    '<b>', '</b>'));

                return $this->redirect()->toRoute('user/settings');
            }
        } else {
            $editNotificationsForm->get('enf-booking-notifications')->setValue($user->getMeta('notification.bookings', 'true'));
        }

        /* Password form */

        $editPasswordForm = $formElementManager->get('User\Form\EditPasswordForm');

        if ($this->getRequest()->isPost() && $editParam == 'password') {
            $editPasswordForm->setData($this->params()->fromPost());

            if ($editPasswordForm->isValid()) {
                $data = $editPasswordForm->getData();

                $passwordCurrent = $data['epf-pw-current'];
                $passwordNew = $data['epf-pw1'];

                $bcrypt = new Bcrypt();
                $bcrypt->setCost(6);

                if ($bcrypt->verify($passwordCurrent, $user->need('pw'))) {

                    $user->set('pw', $bcrypt->create($passwordNew));
                    $userManager->save($user);

                    $this->flashMessenger()->addSuccessMessage(sprintf($this->t('Your %spassword%s has been updated'),
                        '<b>', '</b>'));

                    return $this->redirect()->toRoute('user/settings');
                } else {
                    $editPasswordForm->get('epf-pw-current')->setMessages(array('This is not your correct password'));
                }
            }
        }

        /* Delete account form */

        $deleteAccountForm = $formElementManager->get('User\Form\DeleteAccountForm');
        $deleteAccountMessage = null;

        if ($this->getRequest()->isPost() && $editParam == 'delete') {
            $deleteAccountForm->setData($this->params()->fromPost());

            if ($deleteAccountForm->isValid()) {
                $data = $deleteAccountForm->getData();

                $why = $data['daf-why'];
                $passwordCurrent = $data['daf-pw-current'];

                $bcrypt = new Bcrypt();
                $bcrypt->setCost(6);

                if ($bcrypt->verify($passwordCurrent, $user->need('pw'))) {

                    $user->set('status', 'deleted');
                    $user->set('last_activity', date('Y-m-d H:i:s'));
                    $user->set('last_ip', $_SERVER['REMOTE_ADDR']);

                    if ($why) {
                        $user->setMeta('deletion.reason', $why);
                    }

                    $userManager->save($user);
                    $userSessionManager->logout();

                    $deleteAccountMessage = sprintf($this->t('Your %suser account has been deleted%s. Good bye!'),
                        '<b>', '</b>');
                } else {
                    $editPasswordForm->get('epf-pw-current')->setMessages(array('This is not your correct password'));
                }
            }
        }

        return array(
            'user' => $user,
            'editPhoneForm' => $editPhoneForm,
            'editEmailForm' => $editEmailForm,
            'editNotificationsForm' => $editNotificationsForm,
            'editPasswordForm' => $editPasswordForm,
            'deleteAccountForm' => $deleteAccountForm,
            'deleteAccountMessage' => $deleteAccountMessage,
        );
    }

}
