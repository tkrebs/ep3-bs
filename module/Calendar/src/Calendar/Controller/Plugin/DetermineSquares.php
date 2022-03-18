<?php

namespace Calendar\Controller\Plugin;

use Exception;
use RuntimeException;
use Square\Manager\SquareManager;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class DetermineSquares extends AbstractPlugin
{

    protected $squareManager;

    public function __construct(SquareManager $squareManager)
    {
        $this->squareManager = $squareManager;
    }

    public function __invoke()
    {
        $controller = $this->getController();

        $visibleSquares = $this->squareManager->getAllVisible();

        try {
            $passedSquares = $controller->params()->fromQuery('squares');

            if ($passedSquares) {
                $passedSids = explode(',', $passedSquares);

                $validRequestedSquares = array();

                foreach ($passedSids as $passedSid) {
                    if (isset($visibleSquares[$passedSid])) {
                        $validRequestedSquares[$passedSid] = $visibleSquares[$passedSid];
                    }
                }

                if ($validRequestedSquares) {
                    $visibleSquares = $validRequestedSquares;
                }
            }

            return $visibleSquares;
        } catch (Exception $e) {
            throw new RuntimeException('The passed calendar squares are invalid');
        }
    }

}