<?php

namespace User\Form;

use Zend\Form\Form;
use Zend\InputFilter\Factory;

class PasswordForm extends Form
{

    public function init()
    {
        $this->setName('pf');

        $this->add(array(
            'name' => 'pf-email',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'pf-email',
                'class' => 'autofocus',
                'style' => 'width: 250px;',
            ),
            'options' => array(
                'label' => 'Email address',
                'label_attributes' => array(
                    'class' => 'symbolic symbolic-email',
                    'label_attributes' => array(
                        'class' => 'symbolic symbolic-email',
                    ),
                ),
            ),
        ));

        $this->add(array(
            'name' => 'pf-submit',
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
            'pf-email' => array(
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'EmailAddress',
                    ),
                ),
            ),
        )));
    }

}