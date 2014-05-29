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
                'label' => 'Mitteilung',
                'notes' => 'Optionale Nachricht wenn schreibgeschützt',
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
                'label' => 'Reihenfolge',
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
                'label' => 'Kapazität',
                'notes' => 'Wieviele Spieler passen auf einen Platz?',
            ),
        ));

        $this->add(array(
            'name' => 'cf-capacity-heterogenic',
            'type' => 'Checkbox',
            'attributes' => array(
                'id' => 'cf-capacity-heterogenic',
            ),
            'options' => array(
                'label' => 'Mehrfachbuchungen',
                'notes' => 'Kann dieser Platz mehrmals gebucht werden bis er voll ist (s. Kapazität)?',
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
                'label' => 'Startzeit',
                'postfix' => 'Uhr',
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
                'label' => 'Endzeit',
                'postfix' => 'Uhr',
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
                'label' => 'Zeitblock',
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
                'label' => 'Zeitblock (min. buchbar)',
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
                'label' => 'Zeitblock (max. buchbar)',
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
                'label' => 'Buchung im Voraus',
                'notes' => 'Wie viele Tage im Voraus<br>kann max. gebucht werden?',
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
                'label' => 'Stornierung',
                'notes' => 'Bis wann darf spätestens storniert werden?',
                'postfix' => 'Hours',
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
                            'message' => 'Bitte geben Sie hier etwas ein',
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
                            'message' => 'Bitte geben Sie hier etwas ein',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'Digits',
                        'options' => array(
                            'message' => 'Bitte geben Sie hier eine Zahl ein',
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
                            'message' => 'Bitte geben Sie hier etwas ein',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'Digits',
                        'options' => array(
                            'message' => 'Bitte geben Sie hier eine Zahl ein',
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
                            'message' => 'Bitte geben Sie hier etwas ein',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => '/^[0-9][0-9]:[0-9][0-9]$/',
                            'message' => 'Bitte geben Sie die Zeit im Format HH:MM ein',
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
                            'message' => 'Bitte geben Sie hier etwas ein',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => '/^[0-9][0-9]:[0-9][0-9]$/',
                            'message' => 'Bitte geben Sie die Zeit im Format HH:MM ein',
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
                            'message' => 'Bitte geben Sie hier etwas ein',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'Digits',
                        'options' => array(
                            'message' => 'Bitte geben Sie hier eine Zahl ein',
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
                            'message' => 'Bitte geben Sie hier etwas ein',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'Digits',
                        'options' => array(
                            'message' => 'Bitte geben Sie hier eine Zahl ein',
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
                            'message' => 'Bitte geben Sie hier etwas ein',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'Digits',
                        'options' => array(
                            'message' => 'Bitte geben Sie hier eine Zahl ein',
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
                            'message' => 'Bitte geben Sie hier etwas ein',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'Digits',
                        'options' => array(
                            'message' => 'Bitte geben Sie hier eine Zahl ein',
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
                            'message' => 'Bitte geben Sie hier etwas ein',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'Digits',
                        'options' => array(
                            'message' => 'Bitte geben Sie hier eine Zahl ein',
                        ),
                    ),
                ),
            ),
        )));
    }

}