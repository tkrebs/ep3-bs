<?php

namespace User\Form;

use Zend\Form\Form;
use Zend\InputFilter\Factory;

class EditPasswordForm extends Form
{

    public function init()
    {
        $this->setName('epf');

        $this->add(array(
            'name' => 'epf-pw-current',
            'type' => 'Password',
            'attributes' => array(
                'id' => 'epf-pw-current',
                'style' => 'width: 235px;',
            ),
            'options' => array(
                'notes' => 'Your current password',
            ),
        ));

        $this->add(array(
            'name' => 'epf-pw1',
            'type' => 'Password',
            'attributes' => array(
                'id' => 'epf-pw1',
                'style' => 'width: 235px;',
            ),
            'options' => array(
                'notes' => 'Your new password',
            ),
        ));

        $this->add(array(
            'name' => 'epf-pw2',
            'type' => 'Password',
            'attributes' => array(
                'id' => 'epf-pw2',
                'style' => 'width: 235px;',
            ),
            'options' => array(
                'notes' => 'Please type your new password again<br>to prevent typing errors',
            ),
        ));

        $this->add(array(
            'name' => 'epf-submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Update password',
                'class' => 'default-button',
            ),
        ));

        /* Input filters */

        $factory = new Factory();

        $this->setInputFilter($factory->createInputFilter(array(
            'epf-pw-current' => array(
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'Please type your password here',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                ),
            ),
            'epf-pw1' => array(
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'Please type a new password here',
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
            'epf-pw2' => array(
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'Please type a new password here',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'Identical',
                        'options' => array(
                            'token' => 'epf-pw1',
                            'message' => 'Both passwords must be identical',
                        ),
                    ),
                ),
            ),
        )));
    }

}
