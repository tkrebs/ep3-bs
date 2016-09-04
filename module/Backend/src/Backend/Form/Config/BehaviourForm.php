<?php

namespace Backend\Form\Config;

use Zend\Form\Form;

class BehaviourForm extends Form
{

    public function init()
    {
        $this->setName('cf');

        $this->add(array(
            'name' => 'cf-maintenance',
            'type' => 'Select',
            'attributes' => array(
                'id' => 'cf-maintenance',
                'style' => 'width: 220px;',
            ),
            'options' => array(
                'label' => 'System',
                'value_options' => array(
                    'false' => 'Enabled',
                    'true' => 'Maintenance',
                ),
                'notes' => 'Essentially disables the system for the public,<br>but allows administrators to still login',
            ),
        ));

        $this->add(array(
            'name' => 'cf-maintenance-message',
            'type' => 'Textarea',
            'attributes' => array(
                'id' => 'cf-maintenance-message',
                'style' => 'width: 320px; height: 48px;',
            ),
            'options' => array(
                'label' => 'Message',
                'notes' => 'This message optionally appears in maintenance mode',
            ),
        ));

        $this->add(array(
            'name' => 'cf-registration',
            'type' => 'Select',
            'attributes' => array(
                'id' => 'cf-registration',
                'style' => 'width: 220px;',
            ),
            'options' => array(
                'label' => 'Registration',
                'value_options' => array(
                    'true' => 'Enabled',
                    'false' => 'Disabled',
                ),
                'notes' => 'Sets if new users are allowed to register',
            ),
        ));

        $this->add(array(
            'name' => 'cf-registration-message',
            'type' => 'Textarea',
            'attributes' => array(
                'id' => 'cf-registration-message',
                'style' => 'width: 320px; height: 48px;',
            ),
            'options' => array(
                'label' => 'Message',
                'notes' => 'This message optionally appears when registration is disabled',
            ),
        ));

        $this->add(array(
            'name' => 'cf-activation',
            'type' => 'Select',
            'attributes' => array(
                'id' => 'cf-activation',
                'style' => 'width: 220px;',
            ),
            'options' => array(
                'label' => 'Activation',
                'value_options' => array(
                    'immediate' => 'Immediately',
                    'manual-email' => 'Manually (per backend)',
                    'email' => 'Automatically (per email)',
                ),
                'notes' => 'Sets how new users are activated after registration',
            ),
        ));

        $this->add(array(
            'name' => 'cf-calendar-days',
            'type' => 'Select',
            'attributes' => array(
                'id' => 'cf-calendar-days',
                'style' => 'width: 96px;',
            ),
            'options' => array(
                'label' => 'Days in calendar',
                'value_options' => array(
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                    '7' => '7',
                    '8' => '8',
                ),
                'notes' => 'Sets how many days are displayed in the calendar',
            ),
        ));

        $this->add(array(
            'name' => 'cf-calendar-day-exceptions',
            'type' => 'Textarea',
            'attributes' => array(
                'id' => 'cf-calendar-day-exceptions',
                'style' => 'width: 320px; min-height: 80px',
            ),
            'options' => array(
                'label' => 'Hide these days',
                'notes' => 'Day names (like Sunday) or concrete dates (like 2016-08-16);<br>Separated by line breaks or commas;<br>Force concrete dates to be shown by adding a plus (like +2016-08-30)',
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
    }

}
