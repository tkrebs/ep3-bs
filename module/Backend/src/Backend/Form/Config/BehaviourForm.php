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
                    'false' => 'Eingeschaltet',
                    'true' => 'Wartungsmodus',
                ),
                'notes' => 'Hiermit kann das System in den Wartungsmodus versetzt werden.<br>Im Wartungsmodus ist der Kalender nicht mehr sichtbar und<br>außer Ihnen kann sich niemand anmelden',
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
                'label' => 'Mitteilung',
                'notes' => 'Diese Nachricht erscheint optional im Wartungsmodus',
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
                'label' => 'Registrierung',
                'value_options' => array(
                    'true' => 'Eingeschaltet',
                    'false' => 'Ausgeschaltet',
                ),
                'notes' => 'Legt fest, ob sich neue Besucher registrieren,<br>also ein eigenes Konto anlegen dürfen',
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
                'label' => 'Mitteilung',
                'notes' => 'Diese Nachricht erscheint optional bei ausgeschalteter Registrierung',
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
                'label' => 'Aktivierung',
                'value_options' => array(
                    'immediate' => 'Sofort',
                    'manual-email' => 'Per Verwaltung (manuell)',
                    'email' => 'Per E-Mail (automatisch)',
                ),
                'notes' => 'Legt fest, wie neue Benutzer aktiviert werden sollen',
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
                'label' => 'Tage im Kalender',
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
                'notes' => 'Legt fest, wieviele Tage im Kalender<br>gleichzeitig angezeigt werden sollen',
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