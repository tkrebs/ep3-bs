<?php

namespace Backend\Form\Booking\Range;

use Zend\Form\Form;
use Zend\InputFilter\Factory;

class EditTimeRangeForm extends Form
{

    public function init()
    {
        $this->setName('bf');

        $this->add(array(
            'name' => 'bf-time-start',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'bf-time-start',
                'style' => 'width: 80px;',
            ),
            'options' => array(
                'label' => 'Time (Start)',
            ),
        ));

        $this->add(array(
            'name' => 'bf-time-end',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'bf-time-end',
                'style' => 'width: 80px;',
            ),
            'options' => array(
                'label' => 'Time (End)',
            ),
        ));

        $this->add(array(
            'name' => 'bf-submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Save',
                'id' => 'bf-submit',
                'class' => 'default-button',
                'style' => 'width: 125px;',
            ),
        ));

        /* Input filters */

        $factory = new Factory();

        $this->setInputFilter($factory->createInputFilter(array(
            'bf-time-start' => array(
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'Please type something here',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => '/^[0-9]?[0-9]:[0-9][0-9]$/',
                            'message' => 'Please provide the time in format HH:MM',
                        ),
                    ),
                ),
            ),
            'bf-time-end' => array(
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'options' => array(
                            'message' => 'Please type something here',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => '/^[0-9]?[0-9]:[0-9][0-9]$/',
                            'message' => 'Please provide the time in format HH:MM',
                        ),
                    ),
                ),
            ),
        )));
    }

}