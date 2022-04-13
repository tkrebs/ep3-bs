<?php

namespace Frontend\View\Helper;

use Zend\Stdlib\RequestInterface;
use Square\Manager\SquareGroupManager;
use Square\Entity\SquareGroup;
use Square\Manager\SquareManager;
use Zend\View\Helper\AbstractHelper;

class SquareGroupList extends AbstractHelper
{

    protected $squareGroupManager;
    protected $squareManager;
    protected $request;

    public function __construct(SquareGroupManager $squareGroupManager, SquareManager $squareManager, 
        RequestInterface $request)
    {

        $this->squareGroupManager = $squareGroupManager;
        $this->squareManager = $squareManager;
        $this->request = $request;
    }
    
    public function __invoke()
    {
        $html = '';
        $view = $this->getView();
        if ($this->squareManager->getMinSquareGroup() > 0)
        {
            $html .= '<div id="calendar-toolbar-groupselector" class="panel">';
            $html .= '<table style="height: 100%;"><tr><td class="responsive-pass-5">';
            $html .= sprintf('<div id="userpanel-status" class="no-wrap">%s</div>', $view->translate('Court types'));
            $html .= "</td><td><select name='group-select' id='group-select'>";
                    
            $view = $this->getView();
            $squareGroups = $this->squareGroupManager->getAll();

            $group = $this->request->getQuery('group-select');

            foreach ($squareGroups as $squareGroup) {
                $html .= '<option value="' . $squareGroup->get('sgid') . '"';
                if ($squareGroup->get('sgid') == $group) $html .= ' selected';
                $html .= '>' . $squareGroup->get('description') . '</option>';
            }

            $html .= '</select></td></tr></table></div>';
        }

        return $html;
    }

}

