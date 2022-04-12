<?php

namespace Frontend\View\Helper;

use Square\Manager\SquareManager;
use Zend\View\Helper\AbstractHelper;

class SquareGroupCheck extends AbstractHelper
{

    protected $squareGroupManager;
    protected $request;

    public function __construct(SquareManager $squareManager)
    {

        $this->squareManager = $squareManager;
    }
    
    public function __invoke()
    {
        return ($this->squareManager->getMinSquareGroup() > 0);
    }

}

