<?php

namespace User\Form;

use Zend\Form\Form;

class EditNotificationsForm extends Form
{

    public function init()
    {
        $this->setName('enf');

        $this->add(array(
            'name' => 'enf-booking-notifications',
            'type' => 'Checkbox',
            'attributes' => array(
                'id' => 'enf-booking-notifications',
            ),
            'options' => array(
                'label' => 'Notify on bookings and cancellations',
                'notes' => 'We can send you confirmations per email',
                'checked_value' => 'true',
                'unchecked_value' => 'false',
            ),
        ));

        $this->add(array(
            'name' => 'enf-submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Update settings',
                'class' => 'default-button',
            ),
        ));
    }

}