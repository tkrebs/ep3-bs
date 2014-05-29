<?php

namespace Backend\Form\ConfigSquare;

use Zend\Form\Form;
use Zend\InputFilter\Factory;

class EditInfoForm extends Form
{

    public function init()
    {
        $this->setName('cf');

        $this->add(array(
            'name' => 'cf-info-pre',
            'type' => 'Textarea',
            'attributes' => array(
                'id' => 'cf-info-pre',
                'class' => 'wysiwyg-editor',
                'style' => 'width: 320px; height: 120px;',
            ),
            'options' => array(
                'label' => 'Info (oben)',
                'notes' => 'Optionaler Infotext, der <b>über</b> den Platzdetails angezeigt wird',
            ),
        ));

        $this->add(array(
            'name' => 'cf-info-post',
            'type' => 'Textarea',
            'attributes' => array(
                'id' => 'cf-info-post',
                'class' => 'wysiwyg-editor',
                'style' => 'width: 320px; height: 120px;',
            ),
            'options' => array(
                'label' => 'Info (unten)',
                'notes' => 'Optionaler Infotext, der <b>unter</b> den Platzdetails angezeigt wird',
            ),
        ));

        $this->add(array(
            'name' => 'cf-rules-text',
            'type' => 'Textarea',
            'attributes' => array(
                'id' => 'cf-rules-text',
                'class' => 'wysiwyg-editor',
                'style' => 'width: 320px; height: 120px;',
            ),
            'options' => array(
                'label' => 'Regeln',
                'notes' => 'Optionale Regeln, die vor der Buchung akzeptiert werden müssen',
            ),
        ));

        $this->add(array(
            'name' => 'cf-rules-document-file',
            'type' => 'File',
            'attributes' => array(
                'id' => 'cf-rules-document-file',
                'accept' => '.pdf',
            ),
            'options' => array(
                'label' => 'Regeln (Datei)',
                'notes' => 'Optionale Regeln als PDF-Datei, die vor der Buchung akzeptiert werden müssen',
            ),
        ));

        $this->add(array(
            'name' => 'cf-rules-document-name',
            'type' => 'Text',
            'attributes' => array(
                'id' => 'cf-rules-document-name',
                'style' => 'width: 250px;',
            ),
            'options' => array(
                'label' => 'Regeln (Dateiname)',
                'notes' => 'Optionaler Name der o.g. PDF-Datei',
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
            'cf-rules-text' => array(
                'required' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                ),
            ),
            'cf-rules-document-file' => array(
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
            'cf-rules-document-name' => array(
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