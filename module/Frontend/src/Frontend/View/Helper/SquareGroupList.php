<?php

namespace Frontend\View\Helper;

use Zend\Stdlib\RequestInterface;
use Square\Manager\SquareGroupManager;
use Square\Entity\SquareGroup;
use Zend\View\Helper\AbstractHelper;

class SquareGroupList extends AbstractHelper
{

    protected $squareGroupManager;
    protected $request;

    public function __construct(SquareGroupManager $squareGroupManager, RequestInterface $request)
    {

        $this->squareGroupManager = $squareGroupManager;
        $this->request = $request;
    }
    
    public function __invoke()
    {
        $view = $this->getView();
        $squareGroups = $this->squareGroupManager->getAll();
        $html = '';
        $group = $this->request->getQuery('group-select');

        foreach ($squareGroups as $squareGroup) {
            $html .= '<option value="' . $squareGroup->get('sgid') . '"';
            error_log('group cycle=' . $squareGroup->get('sgid'), 3, '/tmp/booking.log');
            if ($squareGroup->get('sgid') == $group) $html .= ' selected';
            $html .= '>' . $squareGroup->get('description') . '</option>';
        }

        return $html;
    }

}

