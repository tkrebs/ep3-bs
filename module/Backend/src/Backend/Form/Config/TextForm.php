<?php

namespace Backend\Form\Config;

use Zend\Form\Form;
use Zend\InputFilter\Factory;

class TextForm extends Form
{

    public static $definitions = array(
        'client.name.full' => array('Ihr Name', 'Wird Ihren Besuchern als Betreiber angezeigt.<br>Erscheint z.B. ganz oben neben dem Logo.'),
        'client.name.short' => array('Ihr Kürzel', 'Kurzform, Abkürzung oder Akronym Ihres Namens.<br>Erscheint z.B. in der Betreffzeile von E-Mails.'),
        'client.contact.email' => array('Ihre E-Mail Adresse', 'Wird für Benachrichtigungen des Systems benötigt.<br>Kann auch Benutzern für Hilfe angezeigt werden.'),
        'client.contact.phone' => array('Ihre Telefonnummer', 'Wird für die telefonische Buchung angezeigt.<br>Erscheint z.B. ganz oben in der Kopfleiste.'),
        'client.website' => array('Ihre Webseite', 'Die Internetadresse Ihrer Webseite.<br>Erscheint z.B. ganz oben in der Kopfleiste.'),
        'client.website.contact' => array('Ihre Kontaktseite', 'Die Internetadresse Ihrer Kontaktseite.<br>Erscheint z.B. ganz oben in der Kopfleiste.'),
        'client.website.imprint' => array('Ihr Impressum', 'Die Internetadresse Ihres Impressums.'),
        'service.name.full' => array('Name des Systems', 'Unter diesem Namen präsentiert sich das System.<br>Erscheint z.B. ganz oben neben dem Logo.'),
        'service.name.short' => array('Kürzel des Systems', 'Kurzform, Abkürzung oder Akronym des Systems.<br>Erscheint z.B. in E-Mails.'),
        'service.meta.description' => array('Kurzbeschreibung Ihres Angebotes', 'Am besten ein bis zwei Sätze über Ihr Angebot.'),
        'service.meta.keywords' => array('Stichworte Ihres Angebotes', 'Am besten 10 bis 20 Stichworte über Ihr Angebot.'),
        'subject.square.type' => array('Bezeichnung Ihrer "Plätze"', 'Singular'),
        'subject.square.type.plural' => array('Bezeichnung Ihrer "Plätze"', 'Plural'),
        'subject.square.unit' => array('Bezeichnung Ihrer "Spieler"', 'Singular'),
        'subject.square.unit.plural' => array('Bezeichnung Ihrer "Spieler"', 'Plural'),
        'subject.type' => array('Bezeichnung Ihrer Anlage', 'Erscheint z.B. in der Kopfleiste.<br>Bitte mit kleinem Artikelwort beginnen.'),
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
                            'message' => 'Bitte geben Sie hier etwas ein',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 2,
                            'message' => 'Diese Eingabe muss mindestens drei Zeichen lang sein',
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
                            'message' => 'Bitte geben Sie eine richtige E-Mail Adresse ein.',
                            'messages' => array(
                                'emailAddressInvalidMxRecord' => 'Dieser E-Mail Anbieter existiert leider nicht.',
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