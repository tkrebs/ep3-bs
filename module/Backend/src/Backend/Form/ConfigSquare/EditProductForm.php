<?php

namespace Backend\Form\ConfigSquare;

use Base\Manager\ConfigManager;
use Square\Manager\SquareManager;
use Zend\Form\Form;
use Zend\InputFilter\Factory;

class EditProductForm extends Form
{

    protected $configManager;
    protected $squareManager;

    public function __construct(ConfigManager $configManager, SquareManager $squareManager)
    {
        parent::__construct();

        $this->configManager = $configManager;
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
                'label' => 'Description',
                'notes' => 'Optional description of this product',
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
                'label' => 'Options',
                'notes' => 'Amount of products to choose from,<br>e.g. 1,2,3 to choose between 1 and 3 items',
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
                'label' => 'Square',
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
                'label' => 'Priority',
            ),
        ));

        $this->add(array(
            'name' => 'cf-date-start',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'cf-date-start',
                'class' => 'datepicker',
                'style' => 'width: 80px;',
            ),
            'options' => array(
                'label' => 'Date (Start)',
                'notes' => 'Optionally set a date from when<br>this product will be available.<br>Determined from the booked date.',
            ),
        ));

        $this->add(array(
            'name' => 'cf-date-end',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'cf-date-end',
                'class' => 'datepicker',
                'style' => 'width: 80px;',
            ),
            'options' => array(
                'label' => 'Date (End)',
                'notes' => 'Optionally set a date until<br>this product will be available.<br>Determined from the booked date.',
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
                'label' => 'Price',
                'notes' => 'Price per item',
                'postfix' => $this->configManager->get('i18n.currency'),
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
                            'message' => 'Please type something here',
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
                            'message' => 'Please type something here',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'Regex',
                        'options' => array(
                            'pattern' => '/^[0-9\, ]+$/u',
                            'message' => 'Please type a number here',
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
            'cf-date-start' => array(
                'required' => false,
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
                        'name' => 'Callback',
                        'options' => array(
                            'callback' => function($value) {
                                    try {
                                        new \DateTime($value);

                                        return true;
                                    } catch (\Exception $e) {
                                        return false;
                                    }
                                },
                            'message' => 'Invalid date',
                        ),
                    ),
                ),
            ),
            'cf-date-end' => array(
                'required' => false,
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
                        'name' => 'Callback',
                        'options' => array(
                            'callback' => function($value) {
                                    try {
                                        new \DateTime($value);

                                        return true;
                                    } catch (\Exception $e) {
                                        return false;
                                    }
                                },
                            'message' => 'Invalid date',
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
                            'message' => 'Please type a number here',
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