<?php

namespace User\Form;

use User\Manager\UserManager;
use Zend\Form\Form;
use Zend\InputFilter\Factory;

class EditEmailForm extends Form
{

    protected $userManager;

    public function __construct(UserManager $userManager)
    {
        parent::__construct();

        $this->userManager = $userManager;
    }

    public function init()
    {
        $this->setName('eef');

        $this->add(array(
            'name' => 'eef-email1',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'eef-email1',
                'style' => 'width: 235px;',
            ),
            'options' => array(
                'notes' => 'Please provide your email address',
            ),
        ));

        $this->add(array(
            'name' => 'eef-email2',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'eef-email2',
                'style' => 'width: 235px;',
            ),
            'options' => array(
                'notes' => 'Please type your email address again<br>to prevent typing errors',
            ),
        ));

        $this->add(array(
            'name' => 'eef-submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Update email address',
                'class' => 'default-button',
            ),
        ));

        /* Input filters */

        $userManager = $this->userManager;

        $factory = new Factory();

        $this->setInputFilter($factory->createInputFilter(array(
            'eef-email1' => array(
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
            'eef-email2' => array(
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
                            'token' => 'eef-email1',
                            'message' => array(
                                'Both email addresses must be identical',
                            ),
                        ),
                    ),
                ),
            ),
        )));
    }

}