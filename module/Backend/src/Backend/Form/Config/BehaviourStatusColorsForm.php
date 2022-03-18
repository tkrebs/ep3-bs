<?php

namespace Backend\Form\Config;

use Zend\Form\Form;

class BehaviourStatusColorsForm extends Form
{

    public function init()
    {
        $this->setName('cf');

        $this->add(array(
            'name' => 'cf-status-colors',
            'type' => 'Textarea',
            'attributes' => array(
                'id' => 'cf-status-colors',
                'style' => 'width: 320px; height: 256px;',
            ),
            'options' => array(
                'label' => 'Billing status options',
                'notes' => 'One status option per line and formatted as either:<br>Name<br>Name (internal value)<br>Name (internal value) Color<br>Name Color<br><br>For example:<br>Open (pending) #F00<br>Paid at place #F00<br><br>The following values <b>must</b> exist once:<br>pending, paid, cancelled, uncollectable',
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
