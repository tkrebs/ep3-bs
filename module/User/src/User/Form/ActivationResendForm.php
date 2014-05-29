<?php

namespace User\Form;

use Zend\Form\Form;
use Zend\InputFilter\Factory;

class ActivationResendForm extends Form
{

    public function init()
    {
        $this->setName('arf');

        $this->add(array(
            'name' => 'arf-email',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'arf-email',
                'class' => 'autofocus',
                'style' => 'width: 250px;',
            ),
            'options' => array(
                'label' => 'Email address',
                'label_attributes' => array(
                    'class' => 'symbolic symbolic-email',
                ),
            ),
        ));

        $this->add(array(
            'name' => 'arf-submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Request activation mail',
                'class' => 'default-button',
                'style' => 'width: 200px;',
            ),
        ));

        /* Input filters */

        $factory = new Factory();

        $this->setInputFilter($factory->createInputFilter(array(
            'arf-email' => array(
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'EmailAddress',
                    ),
                ),
            ),
        )));
    }

}