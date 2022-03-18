<?php

namespace Base\View\Helper;

use Zend\Form\ElementInterface;
use Zend\View\Helper\AbstractHelper;

class FormRowCompact extends AbstractHelper
{

    public function __invoke($form, $id)
    {
        $view = $this->getView();

        if ($id instanceof ElementInterface) {
            $formElement = $id;
        } else {
            $formElement = $form->get($id);
        }

        $postfix = $formElement->getOption('postfix');

        if ($postfix) {
            $postfix = sprintf('<span class="default-form-postfix" style="margin-left: 8px;">%s</span>',
                $view->t($postfix));
        }

        $html = sprintf('<tr><td><div class="default-form-label-top small-text gray">%s</div> %s %s %s %s</td></tr>',
            $view->formLabel($formElement),
            $view->formElement($formElement),
            $postfix,
            $view->formElementNotes($formElement),
            $view->formElementErrors($formElement));

        return $html;
    }

}