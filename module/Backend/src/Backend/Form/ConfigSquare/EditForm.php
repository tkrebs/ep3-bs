<?php

namespace Backend\Form\ConfigSquare;

use Square\Entity\Square;
use Zend\Form\Form;
use Zend\InputFilter\Factory;

class EditForm extends Form
{

    public function init()
    {
        $this->setName('cf');

        $this->add(array(
            'name' => 'cf-name',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'cf-name',
                'style' => 'width: 80px;',
            ),
            'options' => array(
                'label' => 'Name',
            ),
        ));

        $this->add(array(
            'name' => 'cf-status',
            'type' => 'Select',
            'attributes' => array(
                'id' => 'cf-status',
                'style' => 'width: 160px',
            ),
            'options' => array(
                'label' => 'Status',
                'value_options' => Square::$statusOptions,
            ),
        ));

        $this->add(array(
            'name' => 'cf-readonly-message',
            'type' => 'Textarea',
            'attributes' => array(
                'id' => 'cf-readonly-message',
                'style' => 'width: 320px; height: 48px;',
            ),
            'options' => array(
                'label' => 'Message',
                'notes' => 'Optional message when readonly',
            ),
        ));

        $this->add(array(
            'name' => 'cf-priority',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'cf-priority',
                'style' => 'width: 80px;',
            ),
            'options' => array(
                'label' => 'Priority',
            ),
        ));

        $this->add(array(
            'name' => 'cf-capacity',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'cf-capacity',
                'style' => 'width: 80px;',
            ),
            'options' => array(
                'label' => 'Capacity',
                'notes' => 'How many players fit into one square?',
            ),
        ));

        $this->add(array(
            'name' => 'cf-capacity-heterogenic',
            'type' => 'Checkbox',
            'attributes' => array(
                'id' => 'cf-capacity-heterogenic',
            ),
            'options' => array(
                'label' => 'Multiple bookings',
                'notes' => 'May this square be booked multiple times until its full?',
            ),
        ));

        $this->add(array(
            'name' => 'cf-public-names',
            'type' => 'Checkbox',
            'attributes' => array(
                'id' => 'cf-public-names',
            ),
            'options' => array(
                'label' => 'Public names',
                'notes' => 'Should the names of the users are publicly visible in the calendar?',
            ),
        ));

        $this->add(array(
            'name' => 'cf-time-start',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'cf-time-start',
                'style' => 'width: 80px;',
            ),
            'options' => array(
                'label' => 'Time (Start)',
                'postfix' => 'Clock',
            ),
        ));

        $this->add(array(
            'name' => 'cf-time-end',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'cf-time-end',
                'style' => 'width: 80px;',
            ),
            'options' => array(
                'label' => 'Time (End)',
                'postfix' => 'Clock',
            ),
        ));

        $this->add(array(
            'name' => 'cf-time-block',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'cf-time-block',
                'style' => 'width: 80px;',
            ),
            'options' => array(
                'label' => 'Time block',
                'postfix' => 'Minutes',
            ),
        ));

        $this->add(array(
            'name' => 'cf-time-block-bookable',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'cf-time-block-bookable',
                'style' => 'width: 80px;',
            ),
            'options' => array(
                'label' => 'Time block (min. bookable)',
                'postfix' => 'Minutes',
            ),
        ));

        $this->add(array(
            'name' => 'cf-time-block-bookable-max',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'cf-time-block-bookable-max',
                'style' => 'width: 80px;',
            ),
            'options' => array(
                'label' => 'Time block (max. bookable)',
                'postfix' => 'Minutes',
            ),
        ));

        $this->add(array(
            'name' => 'cf-range-book',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'cf-range-book',
                'style' => 'width: 80px;',
            ),
            'options' => array(
                'label' => 'Booking range',
                'notes' => 'How many days in advance<br>can squares be booked?',
                'postfix' => 'Days',
            ),
        ));

        $this->add(array(
            'name' => 'cf-range-cancel',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'cf-range-cancel',
                'style' => 'width: 80px;',
            ),
            'options' => array(
                'label' => 'Cancel range',
                'notes' => 'Until when may bookings be cancelled?',
                'postfix' => 'Hours',
            ),
        ));

	    $this->add(array(
            'name' => 'cf-label-free',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'cf-label-free',
                'style' => 'width: 80px;',
            ),
            'options' => array(
                'label' => 'Label for free squares',
                'notes' => 'Custom label for free squares in the calendar; default is <b>Free</b>',
            ),
        ));

        $this->add(array(
            'name' => 'cf-submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Save',
                'class' => 'default-button',
                'style' => 'width: 200px;',
            ),
        ));

        /* Input filters */

        $factory = new Factory();

        $this->setInputFilter($factory->createInputFilter(array(
            'cf-name' => array(
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
                ),
            ),
            'cf-priority' => array(
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
                        'name' => 'Digits',
                        'options' => array(
                            'message' => 'Please type a number here',
                        ),
                    ),
                ),
            ),
            'cf-capacity' => array(
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
                        'name' => 'Digits',
                        'options' => array(
                            'message' => 'Please type a number here',
                        ),
                    ),
                ),
            ),
            'cf-time-start' => array(
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
                            'pattern' => '/^[0-9][0-9]:[0-9][0-9]$/',
                            'message' => 'Please provide the time in format HH:MM',
                        ),
                    ),
                ),
            ),
            'cf-time-end' => array(
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
                            'pattern' => '/^[0-9][0-9]:[0-9][0-9]$/',
                            'message' => 'Please provide the time in format HH:MM',
                        ),
                    ),
                ),
            ),
            'cf-time-block' => array(
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
                        'name' => 'Digits',
                        'options' => array(
                            'message' => 'Please type a number here',
                        ),
                    ),
                ),
            ),
            'cf-time-block-bookable' => array(
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
                        'name' => 'Digits',
                        'options' => array(
                            'message' => 'Please type a number here',
                        ),
                    ),
                ),
            ),
            'cf-time-block-bookable-max' => array(
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
                        'name' => 'Digits',
                        'options' => array(
                            'message' => 'Please type a number here',
                        ),
                    ),
                ),
            ),
            'cf-range-book' => array(
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
                        'name' => 'Digits',
                        'options' => array(
                            'message' => 'Please type a number here',
                        ),
                    ),
                ),
            ),
            'cf-range-cancel' => array(
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
                        'name' => 'Digits',
                        'options' => array(
                            'message' => 'Please type a number here',
                        ),
                    ),
                ),
            ),
	        'cf-label-free' => array(
		        'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
            ),
        )));
    }

}
