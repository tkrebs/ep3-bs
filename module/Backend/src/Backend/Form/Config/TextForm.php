<?php

namespace Backend\Form\Config;

use Zend\Form\Form;
use Zend\InputFilter\Factory;

class TextForm extends Form
{

    public static $definitions = array(
        'client.name.full' => array('Your name', 'Will be shown as the operator of this site.<br>Displayed next to the logo, for example.'),
        'client.name.short' => array('Your abbreviation', 'Short form or abbreviation of your name.<br>Displayed in emails, for example.'),
        'client.contact.email' => array('Your email address', 'Will be used for system notifications.<br>Might also be displayed to users for help.'),
        'client.contact.phone' => array('Your phone number', 'Displayed for booking by phone.'),
        'client.website' => array('Your website', 'The address of your website.<br>Displayed in the header, for example.'),
        'client.website.contact' => array('Your contact page', 'The address of your website\'s contact page.<br>Displayed in the header, for example.'),
        'client.website.imprint' => array('Your imprint page', 'The address of your website\'s imprint page.'),
        'service.name.full' => array('Name of the system', 'The system presents itself under this name.<br>Displayed next to the logo, for example.'),
        'service.name.short' => array('System abbreviation', 'Short form or abbreviation of the system name.<br>Displayed in emails, for example.'),
        'service.meta.description' => array('Description of your service', 'One or two short sentences recommended.'),
        'service.meta.keywords' => array('Keywords for your service', '10-20 keywords recommended.'),
        'subject.square.type' => array('Notation of your "squares"', 'Singular'),
        'subject.square.type.plural' => array('Notation of your "squares"', 'Plural'),
        'subject.square.unit' => array('Notation of your "players"', 'Singular'),
        'subject.square.unit.plural' => array('Notation of your "players"', 'Plural'),
        'subject.type' => array('Name of your facility', 'Displayed in the header, for example.<br>Must start with a lower cased noun marker.'),
    );

    public function init()
    {
        $this->setName('cf');

        /* Generate form elements */

        foreach (self::$definitions as $key => $value) {
            $key = str_replace('.', '_', $key);

            $this->add(array(
                'name' => 'cf-' . $key,
                'type' => 'Text',
                'attributes' => array(
                    'id' => 'cf-' . $key,
                    'style' => 'width: 380px;',
                ),
                'options' => array(
                    'label' => $value[0],
                    'notes' => $value[1],
                ),
            ));
        }

        $this->add(array(
            'name' => 'cf-submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Save',
                'class' => 'default-button',
                'style' => 'width: 200px;',
            ),
        ));

        /* Generate input filters */

        $filters = array();

        foreach (self::$definitions as $key => $value) {
            $formKey = str_replace('.', '_', $key);

            $filters['cf-' . $formKey] = array(
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
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 2,
                            'message' => 'Please type more characters here',
                        ),
                    ),
                ),
            );

            switch ($key) {
                case 'client.contact.email':
                    $filters['cf-' . $formKey]['validators'][] = array(
                        'name' => 'EmailAddress',
                        'options' => array(
                            'useMxCheck' => true,
                            'message' => 'Please type something here',
                            'messages' => array(
                                'emailAddressInvalidMxRecord' => 'We could not verify your email provider',
                            ),
                        ),
                    );
                    break;
            }
        }

        $factory = new Factory();

        $this->setInputFilter($factory->createInputFilter($filters));
    }

}