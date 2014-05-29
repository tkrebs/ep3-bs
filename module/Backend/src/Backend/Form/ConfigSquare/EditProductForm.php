<?php

namespace Backend\Form\ConfigSquare;

use Square\Manager\SquareManager;
use Zend\Form\Form;
use Zend\InputFilter\Factory;

class EditProductForm extends Form
{

    protected $squareManager;

    public function __construct(SquareManager $squareManager)
    {
        parent::__construct();

        $this->squareManager = $squareManager;
    }

    public function init()
    {
        $this->setName('cf');

        $this->add(array(
            'name' => 'cf-name',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'cf-name',
                'style' => 'width: 250px;',
            ),
            'options' => array(
                'label' => 'Name',
            ),
        ));

        $this->add(array(
            'name' => 'cf-description',
            'type' => 'Textarea',
            'attributes' => array(
                'id' => 'cf-description',
                'style' => 'width: 320px; height: 48px;',
            ),
            'options' => array(
                'label' => 'Beschreibung',
                'notes' => 'Optionale Beschreibung dieses Produktes',
            ),
        ));

        $this->add(array(
            'name' => 'cf-options',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'cf-options',
                'style' => 'width: 250px;',
                'value' => '1,2,3,4,5,6,7,8,9,10',
            ),
            'options' => array(
                'label' => 'Optionen',
                'notes' => 'Auswahlmöglichkeiten der Anzahl dieses Produktes,<br>z.B. 1,2,3 um zwischen 1 und 3 Stück zu wählen',
            ),
        ));

        $squareOptions = array(
            'null' => 'All squares',
        );

        foreach ($this->squareManager->getAll() as $sid => $square) {
            $squareOptions[$sid] = $square->get('name');
        }

        $this->add(array(
            'name' => 'cf-square',
            'type' => 'Select',
            'attributes' => array(
                'id' => 'cf-square',
                'style' => 'width: 264px',
            ),
            'options' => array(
                'label' => 'Platz',
                'value_options' => $squareOptions,
            ),
        ));

        $this->add(array(
            'name' => 'cf-priority',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'cf-priority',
                'style' => 'width: 80px;',
                'value' => '1',
            ),
            'options' => array(
                'label' => 'Reihenfolge',
            ),
        ));

        $this->add(array(
            'name' => 'cf-price',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'cf-price',
                'class' => 'right-text',
                'style' => 'width: 75px;',
                'value' => '0',
            ),
            'options' => array(
                'label' => 'Preis',
                'notes' => 'Preis pro Stück',
                'postfix' => '&euro;',
            ),
        ));

        $this->add(array(
            'name' => 'cf-gross',
            'type' => 'Select',
            'attributes' => array(
                'id' => 'cf-gross',
                'style' => 'width: 89px;',
            ),
            'options' => array(
                'label' => ' ',
                'value_options' => array(
                    '1' => 'including',
                    '0' => 'plus',
                ),
            ),
        ));

        $this->add(array(
            'name' => 'cf-rate',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'cf-rate',
                'class' => 'right-text',
                'style' => 'width: 75px;',
                'value' => '19',
            ),
            'options' => array(
                'label' => 'VAT',
                'postfix' => '%',
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
            'cf-description' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
            ),
            'cf-options' => array(
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
                            'pattern' => '/^[0-9\, ]+$/u',
                            'message' => 'Nur Zahlen und Kommata erlaubt',
                        ),
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
            'cf-price' => array(
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => '/^[0-9\,\. ]+$/u',
                            'message' => 'Nur Zahlen und Kommata erlaubt',
                        ),
                    ),
                ),
            ),
            'cf-rate' => array(
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'Digits',
                        'options' => array(
                            'message' => 'Please type a number here',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                ),
            ),
        )));
    }

}