<?php

namespace Base\View\Helper;

use Zend\Form\View\Helper\FormElementErrors as ZendFormElementErrors;

class FormElementErrors extends ZendFormElementErrors
{

    protected $attributes = array(
        'class' => 'default-form-errors message',
    );

}