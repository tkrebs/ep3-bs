<?php

namespace User\Form;

use Zend\Form\Form;
use Zend\InputFilter\Factory;

class PasswordResetForm extends Form
{

    public function init()
    {
        $this->setName('prf');

        $this->add(array(
            'name' => 'prf-pw1',
            'type' => 'Password',
            'attributes' => array(
                'id' => 'prf-pw1',
                'class' => 'autofocus',
                'style' => 'width: 250px;',
            ),
            'options' => array(
                'label' => 'New password',
                'label_attributes' => array(
                    'class' => 'symbolic symbolic-pw',
                ),
                'notes' => 'Your password will be safely encrypted',
            ),
        ));

        $this->add(array(
            'name' => 'prf-pw2',
            'type' => 'Password',
            'attributes' => array(
                'id' => 'prf-pw2',
                'style' => 'width: 250px;',
            ),
            'options' => array(
                'label' => ' ',
                'notes' => 'Please type your password again',
            ),
        ));

        $this->add(array(
            'name' => 'prf-submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Change password',
                'class' => 'default-button',
                'style' => 'width: 175px;',
            ),
        ));

        /* Input filters */

        $factory = new Factory();

        $this->setInputFilter($factory->createInputFilter(array(
            'prf-pw1' => array(
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'Please type your password',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 4,
                            'message' => 'Your new password should be at least %min% characters long',
                        ),
                    ),
                ),
            ),
            'prf-pw2' => array(
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
                            'token' => 'prf-pw1',
                            'message' => 'Both passwords must be identical',
                        ),
                    ),
                ),
            ),
        )));
    }

}
