<?php

namespace User\Form;

use Base\Manager\OptionManager;
use User\Entity\User;
use User\Manager\UserManager;
use Zend\Crypt\Password\Bcrypt;
use Zend\Form\Form;
use Zend\InputFilter\Factory;

class RegistrationForm extends Form
{

    protected $optionManager;
    protected $userManager;

    public function __construct(OptionManager $optionManager, UserManager $userManager)
    {
        parent::__construct();

        $this->optionManager = $optionManager;
        $this->userManager = $userManager;
    }

    public function init()
    {
        $this->setName('rf');

        /* Credentials */

        $this->add(array(
            'name' => 'rf-email1',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'rf-email1',
                'class' => 'autofocus',
                'style' => 'width: 250px;',
            ),
            'options' => array(
                'label' => 'Email address',
                'label_attributes' => array(
                    'class' => 'symbolic symbolic-email',
                ),
                'notes' => 'Please provide your email address',
            ),
        ));

        $this->add(array(
            'name' => 'rf-email2',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'rf-email2',
                'style' => 'width: 250px;',
            ),
            'options' => array(
                'label' => ' ',
                'notes' => 'Please type your email address again<br>to prevent typing errors',
            ),
        ));

        $this->add(array(
            'name' => 'rf-pw1',
            'type' => 'Password',
            'attributes' => array(
                'id' => 'rf-pw1',
                'style' => 'width: 250px;',
            ),
            'options' => array(
                'label' => 'Password',
                'label_attributes' => array(
                    'class' => 'symbolic symbolic-pw',
                ),
                'notes' => 'Your password will be safely encrypted',
            ),
        ));

        $this->add(array(
            'name' => 'rf-pw2',
            'type' => 'Password',
            'attributes' => array(
                'id' => 'rf-pw2',
                'style' => 'width: 250px;',
            ),
            'options' => array(
                'label' => ' ',
                'notes' => 'Please type your password again<br>to prevent typing errors',
            ),
        ));

        /* Personal data */

        $this->add(array(
            'name' => 'rf-gender',
            'type' => 'Select',
            'attributes' => array(
                'id' => 'rf-gender',
            ),
            'options' => array(
                'label' => 'Salutation',
                'value_options' => User::$genderOptions,
            ),
        ));

        $this->add(array(
            'name' => 'rf-firstname',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'rf-firstname',
                'style' => 'width: 116px;',
            ),
            'options' => array(
                'label' => 'First & Last name',
            ),
        ));

        $this->add(array(
            'name' => 'rf-lastname',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'rf-lastname',
                'style' => 'width: 116px;',
            ),
            'options' => array(
                'label' => 'Last name',
            ),
        ));

        $this->add(array(
            'name' => 'rf-street',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'rf-street',
                'style' => 'width: 182px;',
            ),
            'options' => array(
                'label' => 'Street & Number',
            ),
        ));

        $this->add(array(
            'name' => 'rf-number',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'rf-number',
                'style' => 'width: 50px;',
            ),
            'options' => array(
                'label' => 'Street number',
            ),
        ));

        $this->add(array(
            'name' => 'rf-zip',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'rf-zip',
                'style' => 'width: 116px;',
            ),
            'options' => array(
                'label' => 'Postal code & City',
            ),
        ));

        $this->add(array(
            'name' => 'rf-city',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'rf-city',
                'style' => 'width: 116px;',
            ),
            'options' => array(
                'label' => 'City',
            ),
        ));

        $this->add(array(
            'name' => 'rf-phone',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'rf-phone',
                'style' => 'width: 250px;',
            ),
            'options' => array(
                'label' => 'Phone number',
                'notes' => 'We only use this to inform you<br>about changes to your bookings',
            ),
        ));

        /*
         * Optional birthdate input not allowed anymore by EU GDPR
         *
        $this->add(array(
            'name' => 'rf-birthdate',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'rf-birthdate',
                'style' => 'width: 116px;',
            ),
            'options' => array(
                'label' => 'Birthday',
                'notes' => 'We need this, because ...',
            ),
        ));
        */

        /* Add business terms and privacy policy if configured */

        $termsFile = $this->optionManager->get('service.user.registration.terms.file');

        if ($termsFile) {
            $this->add(array(
                'name' => 'rf-terms',
                'type' => 'Checkbox',
                'attributes' => array(
                    'id' => 'rf-terms',
                ),
                'options' => array(
                    'label' => 'I agree to %s',
                    'checked_value' => 'true',
                    'unchecked_value' => 'false',
                ),
            ));
        }

        $privacyFile = $this->optionManager->get('service.user.registration.privacy.file');

        if ($privacyFile) {
            $this->add(array(
                'name' => 'rf-privacy',
                'type' => 'Checkbox',
                'attributes' => array(
                    'id' => 'rf-privacy',
                ),
                'options' => array(
                    'label' => 'I agree to %s',
                    'checked_value' => 'true',
                    'unchecked_value' => 'false',
                ),
            ));
        }

        /* Add fake nickname to fool spam bots */

        $this->add(array(
            'name' => 'rf-nickname',
            'type' => 'Text',
            'attributes' => array(
                'style' => 'display: none;',
            ),
        ));

        /* Add weak CSRF protection */

        $time = time();

        $bcrypt = new Bcrypt();
        $bcrypt->setCost(6);
        $bcrypt->setSalt(str_pad(php_uname(), 16, '!'));

        $this->add(array(
            'name' => 'rf-csrf',
            'type' => 'Hidden',
            'attributes' => array(
                'value' => $time . $bcrypt->create($time),
            ),
        ));

        $this->add(array(
            'name' => 'rf-submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Complete registration',
                'class' => 'default-button',
                'style' => 'width: 250px;',
            ),
        ));

        /* Input filters */

        $userManager = $this->userManager;

        $factory = new Factory();

        $this->setInputFilter($factory->createInputFilter(array(
            'rf-email1' => array(
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'Please type your email address here',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'EmailAddress',
                        'options' => array(
                            'useMxCheck' => true,
                            'message' => 'Please type your correct email address here',
                            'messages' => array(
                                'emailAddressInvalidMxRecord' => 'We could not verify your email provider',
                            ),
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'Callback',
                        'options' => array(
                            'callback' => function($value) {
                                $blacklist = getcwd() . '/data/res/blacklist-emails.txt';

                                if (is_readable($blacklist)) {
                                    $blacklistContent = file_get_contents($blacklist);
                                    $blacklistDomains = explode("\r\n", $blacklistContent);

                                    foreach ($blacklistDomains as $blacklistDomain) {
                                        $blacklistPattern = str_replace('.', '\.', $blacklistDomain);

                                        if (preg_match('/' . $blacklistPattern . '$/', $value)) {
                                            return false;
                                        }
                                    }
                                }

                                return true;
                            },
                            'message' => 'Trash mail addresses are currently blocked - sorry',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'Callback',
                        'options' => array(
                            'callback' => function($value) use ($userManager) {
                                if ($userManager->getBy(array('email' => $value))) {
                                    return false;
                                } else {
                                    return true;
                                }
                            },
                            'message' => 'This email address has already been registered',
                        ),
                    ),
                ),
            ),
            'rf-email2' => array(
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'Please type your email address here',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'Identical',
                        'options' => array(
                            'token' => 'rf-email1',
                            'message' => array(
                                'Both email addresses must be identical',
                            ),
                        ),
                    ),
                ),
            ),
            'rf-pw1' => array(
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'Please type your password here',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 4,
                            'message' => 'Your password should be at least %min% characters long',
                        ),
                    ),
                ),
            ),
            'rf-pw2' => array(
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'Please type your password here',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'Identical',
                        'options' => array(
                            'token' => 'rf-pw1',
                            'message' => 'Both passwords must be identical',
                        ),
                    ),
                ),
            ),
            'rf-firstname' => array(
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'Please type your name here',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'message' => 'Your name is somewhat short ...',
                        ),
                    ),
                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => '/^([ \&\'\(\)\+\,\-\.0-9\x{00c0}-\x{01ff}a-zA-Z])+$/u',
                            'message' => 'Your name contains invalid characters - sorry',
                        ),
                    ),
                ),
            ),
            'rf-lastname' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'message' => 'Your last name is somewhat short ...',
                        ),
                    ),
                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => '/^([ \'\+\-\x{00c0}-\x{01ff}a-zA-Z])+$/u',
                            'message' => 'Your last name contains invalid characters - sorry',
                        ),
                    ),
                ),
            ),
            'rf-street' => array(
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'Callback', 'options' => array('callback' => function($name) { return ucfirst($name); })),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'Please type your street name here',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 2,
                            'message' => 'This street name is somewhat short ...',
                        ),
                    ),
                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => '/^([ \.\'\-\x{00c0}-\x{01ff}a-zA-Z0-9])+$/u',
                            'message' => 'This street name contains invalid characters - sorry',
                        ),
                    ),
                ),
            ),
            'rf-number' => array(
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'Please type your street number here',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => '/^([0-9a-zA-Z\.\-\/])+$/u',
                            'message' => 'This street number contains invalid characters - sorry',
                        ),
                    ),
                ),
            ),
            'rf-zip' => array(
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'Please type your postal code here',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => '/^[0-9]{4,6}$/',
                            'message' => 'Please provide a correct postal code',
                        ),
                    ),
                ),
            ),
            'rf-city' => array(
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'Callback', 'options' => array('callback' => function($name) { return ucfirst($name); })),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'Please type your city here',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'message' => 'This city name is somewhat short ...',
                        ),
                    ),
                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => '/^([ \&\'\(\)\.\-\x{00c0}-\x{01ff}a-zA-Z])+$/u',
                            'message' => 'This city name contains invalid characters - sorry',
                        ),
                    ),
                ),
            ),
            'rf-phone' => array(
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'Please type your phone number here',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'message' => 'This phone number is somewhat short ...',
                        ),
                    ),
                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => '/^([ \+\/\(\)\-0-9])+$/u',
                            'message' => 'This phone number contains invalid characters - sorry',
                        ),
                    ),
                ),
            ),
            /*
            'rf-birthdate' => array(
                'required' => true,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
            ),
            */
            'rf-terms' => array(
                'required' => false,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'Please accept this',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'Callback',
                        'options' => array(
                            'callback' => function($value) {
                                return $value === 'true';
                            },
                            'message' => 'Please agree to this',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                ),
            ),
            'rf-privacy' => array(
                'required' => false,
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'Please accept this',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'Callback',
                        'options' => array(
                            'callback' => function($value) {
                                return $value === 'true';
                            },
                            'message' => 'Please agree to this',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                ),
            ),
            'rf-nickname' => array(
                'required' => false,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'max' => 0,
                            'message' => 'Please leave this field empty',
                        ),
                    ),
                ),
            ),
            'rf-csrf' => array(
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'Please register over our website only',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'Callback',
                        'options' => array(
                            'callback' => function($value) use ($bcrypt) {
                                $time = time();

                                $formTime = substr($value, 0, strlen($time));
                                $formTimeHash = substr($value, strlen($time));

                                if ($formTimeHash != $bcrypt->create($formTime)) {
                                    return false;
                                }

                                // Allow form submission after five seconds and until one hour
                                if (time() - $formTime < 5 || time() - $formTime > 60 * 60) {
                                    return false;
                                } else {
                                    return true;
                                }
                            },
                            'message' => 'You were too quick for our system! Please wait some seconds and try again. Thank you!',
                        ),
                    ),
                ),
            ),
        )));
    }

}
