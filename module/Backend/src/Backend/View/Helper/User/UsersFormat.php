<?php

namespace Backend\View\Helper\User;

use Zend\View\Helper\AbstractHelper;

class UsersFormat extends AbstractHelper
{

    public function __invoke(array $users, $search = null)
    {
        $view = $this->getView();
        $html = '';

        $html .= '<table class="bordered-table">';

        $html .= '<tr class="gray">';
        $html .= '<th>' . $view->t('No.') . '</th>';
        $html .= '<th>' . $view->t('Name') . '</th>';
        $html .= '<th>' . $view->t('Status') . '</th>';
        $html .= '<th class="email-col">' . $view->t('Email address') . '</th>';
        $html .= '<th class="notes-col">' . $view->t('Notes') . '</th>';
        $html .= '<th class="no-print">&nbsp;</th>';
        $html .= '</tr>';

        foreach ($users as $user) {
            $html .= $view->backendUserFormat($user, $search);
        }

        $html .= '</table>';

        $html .= '<style type="text/css"> .actions-col { border: none !important; } </style>';

        $view->headScript()->appendFile($view->basePath('js/controller/backend/user/index.min.js'));

        return $html;
    }

}