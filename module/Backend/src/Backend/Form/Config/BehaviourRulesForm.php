<?php

namespace Backend\Form\Config;

use Zend\Form\Form;
use Zend\InputFilter\Factory;

class BehaviourRulesForm extends Form
{

    public function init()
    {
        $this->setName('cf');

        $this->add(array(
            'name' => 'cf-terms-file',
            'type' => 'File',
            'attributes' => array(
                'id' => 'cf-terms-file',
                'accept' => '.pdf',
            ),
            'options' => array(
                'label' => 'Business terms (file)',
                'notes' => 'Optional business terms as PDF-Document that must be accepted prior to registration',
            ),
        ));

        $this->add(array(
            'name' => 'cf-terms-name',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'cf-terms-name',
                'style' => 'width: 250px;',
            ),
            'options' => array(
                'label' => 'Business terms (file name)',
                'notes' => 'Optional file name of the PDF-Document above',
            ),
        ));

        $this->add(array(
            'name' => 'cf-privacy-file',
            'type' => 'File',
            'attributes' => array(
                'id' => 'cf-privacy-file',
                'accept' => '.pdf',
            ),
            'options' => array(
                'label' => 'Privacy policy (file)',
                'notes' => 'Optional privacy policy as PDF-Document that must be accepted prior to registration',
            ),
        ));

        $this->add(array(
            'name' => 'cf-privacy-name',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'cf-privacy-name',
                'style' => 'width: 250px;',
            ),
            'options' => array(
                'label' => 'Privacy policy (file name)',
                'notes' => 'Optional file name of the PDF-Document above',
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
            'cf-terms-file' => array(
                'required' => false,
                'validators' => array(
                    array(
                        'name' => 'File/MimeType',
                        'options' => array(
                            'mimeType' => 'application/pdf',
                            'message' => 'The selected file must be a PDF document file',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'File/Size',
                        'options' => array(
                            'min' => '2kB',
                            'max' => '4MB',
                            'message' => 'The selected document\'s file size must be between 2 kB and 4 MB',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                ),
            ),
            'cf-terms-name' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'message' => 'The name should be at least %min% characters long',
                        ),
                    ),
                ),
            ),
            'cf-privacy-file' => array(
                'required' => false,
                'validators' => array(
                    array(
                        'name' => 'File/MimeType',
                        'options' => array(
                            'mimeType' => 'application/pdf',
                            'message' => 'The selected file must be a PDF document file',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                    array(
                        'name' => 'File/Size',
                        'options' => array(
                            'min' => '2kB',
                            'max' => '4MB',
                            'message' => 'The selected document\'s file size must be between 2 kB and 4 MB',
                        ),
                        'break_chain_on_failure' => true,
                    ),
                ),
            ),
            'cf-privacy-name' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'min' => 3,
                            'message' => 'The name should be at least %min% characters long',
                        ),
                    ),
                ),
            ),
        )));
    }

}