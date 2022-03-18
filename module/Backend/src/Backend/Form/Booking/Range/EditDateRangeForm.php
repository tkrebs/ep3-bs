<?php

namespace Backend\Form\Booking\Range;

use Booking\Entity\Booking;
use Zend\Form\Form;
use Zend\InputFilter\Factory;

class EditDateRangeForm extends Form
{

    public function init()
    {
        $this->setName('bf');

        $this->add(array(
            'name' => 'bf-date-start',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'bf-date-start',
                'style' => 'width: 80px;',
            ),
            'options' => array(
                'label' => 'Date (Start)',
            ),
        ));

        $this->add(array(
            'name' => 'bf-date-end',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'bf-date-end',
                'style' => 'width: 80px;',
            ),
            'options' => array(
                'label' => 'Date (End)',
            ),
        ));

        $this->add(array(
            'name' => 'bf-repeat',
            'type' => 'Select',
            'attributes' => array(
                'id' => 'bf-repeat',
                'style' => 'width: 124px',
            ),
            'options' => array(
                'label' => 'Repeat',
                'value_options' => Booking::$repeatOptions,
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
            'bf-date-start' => array(
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
                        'name' => 'Callback',
                        'options' => array(
                            'callback' => function($value) {
                                try {
                                    new \DateTime($value);

                                    return true;
                                } catch (\Exception $e) {
                                    return false;
                                }
                            },
                            'message' => 'Invalid date',
                        ),
                    ),
                ),
            ),
            'bf-date-end' => array(
                'required' => false,
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
                        'name' => 'Callback',
                        'options' => array(
                            'callback' => function($value) {
                                try {
                                    new \DateTime($value);

                                    return true;
                                } catch (\Exception $e) {
                                    return false;
                                }
                            },
                            'message' => 'Invalid date',
                        ),
                    ),
                ),
            ),
        )));
    }

}