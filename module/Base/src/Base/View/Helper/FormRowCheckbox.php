<?php

namespace Base\View\Helper;

use Zend\Form\ElementInterface;
use Zend\View\Helper\AbstractHelper;

class FormRowCheckbox extends AbstractHelper
{

    public function __invoke($form, $id)
    {
        $view = $this->getView();

        if ($id instanceof ElementInterface) {
            $formElement = $id;
        } else {
            $formElement = $form->get($id);
        }

        $html = sprintf('<tr><td class="default-form-label-row">&nbsp;</td><td>%s %s %s %s</td></tr>',
            $view->formElement($formElement),
            $view->formLabel($formElement),
            $view->formElementNotes($formElement),
            $view->formElementErrors($formElement));

        return $html;
    }

}