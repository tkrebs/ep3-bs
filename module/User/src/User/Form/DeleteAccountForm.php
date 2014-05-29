<?php

namespace User\Form;

use Zend\Form\Form;
use Zend\InputFilter\Factory;

class DeleteAccountForm extends Form
{

    public function init()
    {
        $this->setName('daf');

        $this->add(array(
            'name' => 'daf-why',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'daf-why',
                'style' => 'width: 640px;',
            ),
            'options' => array(
                'notes' => 'Were you not happy with our service? Please tell us why you leave. Thank you!',
            ),
        ));

        $this->add(array(
            'name' => 'daf-pw-current',
            'type' => 'Password',
            'attributes' => array(
                'id' => 'daf-pw-current',
                'style' => 'width: 235px;',
            ),
            'options' => array(
                'notes' => 'Your current password',
            ),
        ));

        $this->add(array(
            'name' => 'daf-submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Delete account',
                'class' => 'default-button',
            ),
        ));

        /* Input filters */

        $factory = new Factory();

        $this->setInputFilter($factory->createInputFilter(array(
            'daf-why' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags'),
                ),
            ),
            'daf-pw-current' => array(
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
        )));
    }

}