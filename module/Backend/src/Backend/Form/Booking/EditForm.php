<?php

namespace Backend\Form\Booking;

use Booking\Entity\Booking;
use Booking\Service\BookingStatusService;
use Square\Manager\SquareManager;
use Zend\Form\Form;
use Zend\InputFilter\Factory;

class EditForm extends Form
{

    protected $bookingStatusService;
    protected $squareManager;

    public function __construct(BookingStatusService $bookingStatusService, SquareManager $squareManager)
    {
        parent::__construct();

        $this->bookingStatusService = $bookingStatusService;
        $this->squareManager = $squareManager;
    }

    public function init()
    {
        $this->setName('bf');

        $this->add(array(
            'name' => 'bf-rid',
            'type' => 'Hidden',
            'attributes' => array(
                'id' => 'bf-rid',
            ),
        ));

        $this->add(array(
            'name' => 'bf-user',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'bf-user',
                'style' => 'width: 200px;',
            ),
            'options' => array(
                'label' => 'Booked to',
            ),
        ));

        $squareOptions = array();

        foreach ($this->squareManager->getAll() as $sid => $square) {
            $squareOptions[$sid] = $square->get('name');
        }

        $this->add(array(
            'name' => 'bf-sid',
            'type' => 'Select',
            'attributes' => array(
                'id' => 'bf-sid',
                'style' => 'width: 124px',
            ),
            'options' => array(
                'label' => 'Square',
                'value_options' => $squareOptions,
            ),
        ));

        $this->add(array(
            'name' => 'bf-status-billing',
            'type' => 'Select',
            'attributes' => array(
                'id' => 'bf-status-billing',
                'style' => 'width: 124px',
            ),
            'options' => array(
                'label' => 'Billing status',
                'value_options' => $this->bookingStatusService->getStatusTitles(),
            ),
        ));

        $this->add(array(
            'name' => 'bf-quantity',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'bf-quantity',
                'style' => 'width: 110px;',
                'value' => '1',
            ),
            'options' => array(
                'label' => 'Number of players',
            ),
        ));

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
            'name' => 'bf-notes',
            'type' => 'Textarea',
            'attributes' => array(
                'id' => 'bf-notes',
                'style' => 'width: 250px; height: 48px;',
            ),
            'options' => array(
                'label' => 'Notes',
                'notes' => 'These are only visible for administration',
            ),
        ));

        $this->add(array(
            'name' => 'bf-submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Save',
                'id' => 'bf-submit',
                'class' => 'default-button',
                'style' => 'width: 200px;',
            ),
        ));

        /* Input filters */

        $factory = new Factory();

        $this->setInputFilter($factory->createInputFilter(array(
            'bf-user' => array(
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
            'bf-quantity' => array(
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
            'bf-notes' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
            ),
        )));
    }

}
