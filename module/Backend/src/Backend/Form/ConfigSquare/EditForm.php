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
            'name' => 'cf-capacity-ask-names',
            'type' => 'Select',
            'attributes' => array(
                'id' => 'cf-capacity-ask-names',
                'style' => 'width: 270px',
            ),
            'options' => array(
                'label' => ' ',
                'empty_option' => 'Don\'t ask for other player\'s names',
                'value_options' => array(
                    'optional-names' => 'Ask for other player\'s names (optional)',
                    'optional-names-email' => 'Ask for other player\'s names and email address (optional)',
                    'optional-names-phone' => 'Ask for other player\'s names and phone number (optional)',
                    'optional-names-email-phone' => 'Ask for other player\'s names, email address and phone number (optional)',
                    'required-names' => 'Ask for other player\'s names (required)',
                    'required-names-email' => 'Ask for other player\'s names and email address (required)',
                    'required-names-phone' => 'Ask for other player\'s names and phone number (required)',
                    'required-names-email-phone' => 'Ask for other player\'s names, email address and phone number (required)',
                ),
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
            'name' => 'cf-name-visibility',
            'type' => 'Select',
            'attributes' => array(
                'id' => 'cf-name-visibility',
                'style' => 'width: 270px',
            ),
            'options' => array(
                'label' => 'Visibility of names',
                'empty_option' => 'None',
                'value_options' => array(
                    'private' => 'For other users that are logged in',
                    'public' => 'Publicly for everyone'
                ),
                'notes' => 'Who should see the names of the booking users in the calendar?',
            ),
        ));

        $this->add(array(
            'name' => 'cf-allow-notes',
            'type' => 'Checkbox',
            'attributes' => array(
                'id' => 'cf-allow-notes',
            ),
            'options' => array(
                'label' => 'Erlaube optionale Anmerkungen bei der Buchung',
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
            'name' => 'cf-pseudo-time-block-bookable',
            'type' => 'Checkbox',
            'attributes' => array(
                'id' => 'cf-pseudo-time-block-bookable',
            ),
            'options' => array(
                'label' => 'Allow min. bookable time block for admins only',
                'notes' => 'Users still can only book the normal time blocks then',
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
            'name' => 'cf-min-range-book',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'cf-min-range-book',
                'style' => 'width: 80px;',
            ),
            'options' => array(
                'label' => 'Buchungsvorlauf',
                'notes' => 'Auf 0 setzen, um den nächsten freien Zeitblock buchen zu dürfen',
                'postfix' => 'Minuten',
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
            'name' => 'cf-max-active-bookings',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'cf-max-active-bookings',
                'style' => 'width: 80px;',
            ),
            'options' => array(
                'label' => 'Buchungen einschränken',
                'notes' => 'Auf 0 setzen, um beliebig viele Buchungen zu erlauben',
                'postfix' => 'gleichzeitige Buchung(en) pro Benutzer',
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
                'notes' => 'Until when may bookings be cancelled?<br>Set to 0 to never allow.<br>Set to 0.01 for some seconds (practically always).',
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
            'cf-capacity-ask-names' => array(
                'required' => false,
            ),
            'cf-name-visibility' => array(
                'required' => false,
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
            'cf-min-range-book' => array(
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
            'cf-max-active-bookings' => array(
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
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => '~^[0-9]+(\.[0-9]{1,2})?$~',
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
