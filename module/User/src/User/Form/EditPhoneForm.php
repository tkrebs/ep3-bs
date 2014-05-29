<?php

namespace User\Form;

use Zend\Form\Form;
use Zend\InputFilter\Factory;

class EditPhoneForm extends Form
{

    public function init()
    {
        $this->setName('epf');

        $this->add(array(
            'name' => 'epf-phone',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'epf-phone',
                'style' => 'width: 235px;',
            ),
            'options' => array(
                'notes' => 'We only use this to inform you<br>about changes to your bookings',
            ),
        ));

        $this->add(array(
            'name' => 'epf-submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Update phone number',
                'class' => 'default-button',
            ),
        ));

        /* Input filters */

        $factory = new Factory();

        $this->setInputFilter($factory->createInputFilter(array(
            'epf-phone' => array(
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
        )));
    }

}