<?php

namespace Backend\Form\User;

use User\Entity\User;
use User\Manager\UserManager;
use Zend\Db\Sql\Predicate\IsNotNull;
use Zend\Form\Form;
use Zend\InputFilter\Factory;

class EditForm extends Form
{

    protected $userManager;

    public function __construct(UserManager $userManager)
    {
        parent::__construct();

        $this->userManager = $userManager;
    }

    public function init()
    {
        $this->setName('euf');

        /* Account data */

        $this->add(array(
            'name' => 'euf-uid',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'euf-uid',
                'style' => 'width: 116px;',
                'readonly' => true,
            ),
            'options' => array(
                'label' => 'No.',
            ),
        ));

        $this->add(array(
            'name' => 'euf-alias',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'euf-alias',
                'style' => 'width: 250px;',
            ),
            'options' => array(
                'label' => 'Alias',
                'notes' => 'Arbitrary name or identifier for this user',
            ),
        ));

        $this->add(array(
            'name' => 'euf-status',
            'type' => 'Select',
            'attributes' => array(
                'id' => 'euf-status',
                'style' => 'width: 264px',
            ),
            'options' => array(
                'label' => 'Status',
                'value_options' => User::$statusOptions,
            ),
        ));

        $this->add(array(
            'name' => 'euf-privileges',
            'type' => 'Select',
            'attributes' => array(
                'id' => 'euf-privileges',
                'style' => 'width: 264px',
                'multiple' => 'multiple',
                'size' => count(User::$statusOptions),
            ),
            'options' => array(
                'label' => 'Privileges',
                'value_options' => User::$privileges,
                'notes' => 'Press CTRL to select multiple items',
            ),
        ));

        $this->add(array(
            'name' => 'euf-email',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'euf-email',
                'style' => 'width: 250px;',
            ),
            'options' => array(
                'label' => 'Email address',
            ),
        ));

        $this->add(array(
            'name' => 'euf-pw',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'euf-pw',
                'style' => 'width: 250px;',
            ),
            'options' => array(
                'label' => 'New password',
            ),
        ));

        /* Personal data */

        $this->add(array(
            'name' => 'euf-gender',
            'type' => 'Select',
            'attributes' => array(
                'id' => 'euf-gender',
            ),
            'options' => array(
                'label' => 'Salutation',
                'value_options' => array_merge( ['-' => '-'], User::$genderOptions ),
            ),
        ));

        $this->add(array(
            'name' => 'euf-firstname',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'euf-firstname',
                'style' => 'width: 116px;',
            ),
            'options' => array(
                'label' => 'First & Last name',
            ),
        ));

        $this->add(array(
            'name' => 'euf-lastname',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'euf-lastname',
                'style' => 'width: 116px;',
            ),
            'options' => array(
                'label' => 'Last name',
            ),
        ));

        $this->add(array(
            'name' => 'euf-street',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'euf-street',
                'style' => 'width: 250px;',
            ),
            'options' => array(
                'label' => 'Street & Number',
            ),
        ));

        $this->add(array(
            'name' => 'euf-zip',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'euf-zip',
                'style' => 'width: 116px;',
            ),
            'options' => array(
                'label' => 'Postal code & City',
            ),
        ));

        $this->add(array(
            'name' => 'euf-city',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'euf-city',
                'style' => 'width: 116px;',
            ),
            'options' => array(
                'label' => 'City',
            ),
        ));

        $this->add(array(
            'name' => 'euf-phone',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'euf-phone',
                'style' => 'width: 250px;',
            ),
            'options' => array(
                'label' => 'Phone number',
            ),
        ));

        $this->add(array(
            'name' => 'euf-birthdate',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'euf-birthdate',
                'style' => 'width: 116px;',
            ),
            'options' => array(
                'label' => 'Birthday',
            ),
        ));

        $this->add(array(
            'name' => 'euf-notes',
            'type' => 'Textarea',
            'attributes' => array(
                'id' => 'euf-notes',
                'style' => 'width: 250px; height: 48px;',
            ),
            'options' => array(
                'label' => 'Notes',
                'notes' => 'These are only visible for administration',
            ),
        ));

        $this->add(array(
            'name' => 'euf-submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Save',
                'class' => 'default-button',
                'style' => 'width: 200px;',
            ),
        ));

        /* Input filters */

        $userManager = $this->userManager;
        $that = $this;

        $factory = new Factory();

        $this->setInputFilter($factory->createInputFilter(array(
            'euf-uid' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Digits',
                        'options' => array(
                            'message' => 'No. must be a number',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                ),
            ),
            'euf-alias' => array(
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'Please type a name here',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'message' => 'The name should be at least %min% characters long',
                        ),
                    ),
                    array(
                        'name' => 'Callback',
                        'options' => array(
                            'callback' => function($value) {
                                return !is_numeric($value);
                            },
                            'message' => 'The name must not be a number',
                        ),
                    ),
                ),
            ),
            'euf-privileges' => array(
                'required' => false,
            ),
            'euf-email' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'Please type the email address here',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'EmailAddress',
                        'options' => array(
                            'useMxCheck' => true,
                            'message' => 'Please type the correct email address here',
                            'messages' => array(
                                'emailAddressInvalidMxRecord' => 'Could not verify the email provider',
                            ),
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'Callback',
                        'options' => array(
                            'callback' => function($value) use ($userManager, $that) {
                                    $uid = $that->get('euf-uid')->getValue();

                                    if ($uid) {
                                        $user = $userManager->get($uid);
                                    } else {
                                        $user = null;
                                    }

                                    $usersWithEmail = $userManager->getBy(array('email' => $value, new IsNotNull('pw')));

                                    if ($usersWithEmail) {
                                        $userWithEmail = current($usersWithEmail);

                                        if ($user) {
                                            if ($user->need('uid') == $userWithEmail->need('uid')) {
                                                return true;
                                            }
                                        }

                                        return false;
                                    } else {
                                        return true;
                                    }
                                },
                            'message' => 'This email address is already in use',
                        ),
                    ),
                ),
            ),
            'euf-pw' => array(
                'required' => false,
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 4,
                            'message' => 'The password should be at least %min% characters long',
                        ),
                    ),
                ),
            ),
            'euf-firstname' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'message' => 'Firstname should be at least %min% characters long',
                        ),
                    ),
                ),
            ),
            'euf-lastname' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'message' => 'Lastname should be at least %min% characters long',
                        ),
                    ),
                ),
            ),
            'euf-street' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'Callback', 'options' => array('callback' => function($name) { return ucfirst($name); })),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'message' => 'Street should be at least %min% characters long',
                        ),
                    ),
                ),
            ),
            'euf-zip' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'message' => 'Postal code should be at least %min% characters long',
                        ),
                    ),
                ),
            ),
            'euf-city' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'Callback', 'options' => array('callback' => function($name) { return ucfirst($name); })),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'message' => 'Postal code should be at least %min% characters long',
                        ),
                    ),
                ),
            ),
            'euf-phone' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'message' => 'Phone number code should be at least %min% characters long',
                        ),
                    ),
                ),
            ),
            'euf-birthdate' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
            ),
            'euf-notes' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
            ),
        )));
    }

}
