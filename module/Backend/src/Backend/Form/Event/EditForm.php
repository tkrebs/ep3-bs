<?php

namespace Backend\Form\Event;

use Square\Manager\SquareManager;
use Zend\Form\Form;
use Zend\InputFilter\Factory;

class EditForm extends Form
{

    protected $squareManager;

    public function __construct(SquareManager $squareManager)
    {
        parent::__construct();

        $this->squareManager = $squareManager;
    }

    public function init()
    {
        $this->setName('ef');

        $this->add(array(
            'name' => 'ef-name',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'ef-name',
                'style' => 'width: 320px',
            ),
            'options' => array(
                'label' => 'Name',
            ),
        ));

        $this->add(array(
            'name' => 'ef-description',
            'type' => 'Textarea',
            'attributes' => array(
                'id' => 'ef-description',
                'class' => 'wysiwyg-editor',
                'style' => 'width: 320px; height: 180px;',
            ),
            'options' => array(
                'label' => 'Description',
            ),
        ));

        $this->add(array(
            'name' => 'ef-date-start',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'ef-date-start',
                'class' => 'datepicker',
                'style' => 'width: 110px;',
            ),
            'options' => array(
                'label' => 'Date (Start)',
            ),
        ));

        $this->add(array(
            'name' => 'ef-time-start',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'ef-time-start',
                'style' => 'width: 110px;',
            ),
            'options' => array(
                'label' => 'Time (Start)',
            ),
        ));

        $this->add(array(
            'name' => 'ef-date-end',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'ef-date-end',
                'class' => 'datepicker',
                'style' => 'width: 110px;',
            ),
            'options' => array(
                'label' => 'Date (End)',
            ),
        ));

        $this->add(array(
            'name' => 'ef-time-end',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'ef-time-end',
                'style' => 'width: 110px;',
            ),
            'options' => array(
                'label' => 'Time (End)',
            ),
        ));

        $squareOptions = array(
            'null' => 'All squares',
        );

        foreach ($this->squareManager->getAll() as $sid => $square) {
            $squareOptions[$sid] = $square->get('name');
        }

        $this->add(array(
            'name' => 'ef-sid',
            'type' => 'Select',
            'attributes' => array(
                'id' => 'ef-sid',
                'style' => 'width: 124px',
            ),
            'options' => array(
                'label' => 'Square',
                'value_options' => $squareOptions,
            ),
        ));

        $this->add(array(
            'name' => 'ef-capacity',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'ef-capacity',
                'style' => 'width: 110px;',
            ),
            'options' => array(
                'label' => 'Capacity',
                'notes' => 'How many people can participate?',
            ),
        ));

        $this->add(array(
            'name' => 'ef-notes',
            'type' => 'Textarea',
            'attributes' => array(
                'id' => 'ef-notes',
                'style' => 'width: 250px; height: 48px;',
            ),
            'options' => array(
                'label' => 'Notes',
                'notes' => 'These are only visible for administration',
            ),
        ));

        $this->add(array(
            'name' => 'ef-submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Save',
                'id' => 'ef-submit',
                'class' => 'default-button',
                'style' => 'width: 200px;',
            ),
        ));

        /* Input filters */

        $factory = new Factory();

        $this->setInputFilter($factory->createInputFilter(array(
            'ef-name' => array(
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
            'ef-description' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
            ),
            'ef-date-start' => array(
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
            'ef-time-start' => array(
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
            'ef-date-end' => array(
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
            'ef-time-end' => array(
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
            'ef-capacity' => array(
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
            'ef-notes' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
            ),
        )));
    }

}