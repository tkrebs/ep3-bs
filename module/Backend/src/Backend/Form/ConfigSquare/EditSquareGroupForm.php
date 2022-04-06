<?php

namespace Backend\Form\ConfigSquare;

use Base\Manager\ConfigManager;
use Zend\Form\Form;
use Zend\InputFilter\Factory;

class EditSquareGroupForm extends Form
{

    protected $configManager;

    public function __construct(ConfigManager $configManager)
    {
        parent::__construct();

        $this->configManager = $configManager;

    }

    public function init()
    {
        $this->setName('cf');


        $this->add(array(
            'name' => 'cf-description',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'cf-description',
                'style' => 'width: 320px;',
            ),
            'options' => array(
                'label' => 'Description',
                'notes' => 'Optional description of this product',
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

            'cf-description' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
            ),
        )));
    }

}