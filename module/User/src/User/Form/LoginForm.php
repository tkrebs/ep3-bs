<?php

namespace User\Form;

use Zend\Form\Form;
use Zend\InputFilter\Factory;

class LoginForm extends Form
{

    public function init()
    {
        $this->setName('lf');

        $this->add(array(
            'name' => 'lf-email',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'lf-email',
                'class' => 'autofocus',
                'style' => 'width: 250px;',
            ),
            'options' => array(
                'label' => 'Email address',
                'label_attributes' => array(
                    'class' => 'symbolic symbolic-email',
                ),
            ),
        ));

        $this->add(array(
            'name' => 'lf-pw',
            'type' => 'Password',
            'attributes' => array(
                'id' => 'lf-pw',
                'style' => 'width: 250px;',
            ),
            'options' => array(
                'label' => 'Password',
                'label_attributes' => array(
                    'class' => 'symbolic symbolic-pw',
                ),
            ),
        ));

        $this->add(array(
            'name' => 'lf-submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Login',
                'class' => 'default-button',
                'style' => 'width: 175px;',
            ),
        ));

        /* Input filters */

        $factory = new Factory();

        $this->setInputFilter($factory->createInputFilter(array(
            'lf-email' => array(
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
            'lf-submit' => array(
                'fallback_value' => 'Login',
            ),
        )));
    }

}