<?php

namespace Setup\Form;

use Zend\Form\Form;
use Zend\InputFilter\Factory;

class UserForm extends Form
{

    public function init()
    {
        $this->setName('uf');

        $this->add(array(
            'name' => 'uf-firstname',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'uf-firstname',
                'style' => 'width: 250px;',
            ),
            'options' => array(
                'label' => 'Firstname',
            ),
        ));

        $this->add(array(
            'name' => 'uf-lastname',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'uf-lastname',
                'style' => 'width: 250px;',
            ),
            'options' => array(
                'label' => 'Lastname',
            ),
        ));

        $this->add(array(
            'name' => 'uf-email',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'uf-email',
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
            'name' => 'uf-pw',
            'type' => 'Password',
            'attributes' => array(
                'id' => 'uf-pw',
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
            'name' => 'uf-submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Save',
                'class' => 'default-button',
                'style' => 'width: 175px;',
            ),
        ));

        /* Input filters */

        $factory = new Factory();

        $this->setInputFilter($factory->createInputFilter(array(
            'uf-firstname' => array(
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'message' => 'Firstname should be at least %min% characters long',
                        ),
                    ),
                ),
            ),
            'uf-lastname' => array(
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'message' => 'Lastname should be at least %min% characters long',
                        ),
                    ),
                ),
            ),
            'uf-email' => array(
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
                        'options' => array(
                            'useMxCheck' => false,
                            'message' => 'Please type the correct email address here',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                ),
            ),
            'uf-pw' => array(
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
        )));
    }

}