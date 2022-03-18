<?php

namespace User\Controller\Plugin;

use RuntimeException;
use User\Manager\UserSessionManager;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class Authorize extends AbstractPlugin
{

    protected $userSessionManager;

    public function __construct(UserSessionManager $userSessionManager)
    {
        $this->userSessionManager = $userSessionManager;
    }

    public function __invoke($privileges = null)
    {
        $user = $this->userSessionManager->getSessionUser();

        if (! $user) {
            throw new RuntimeException('You are not logged in (anymore)');
        }

        if ($privileges) {
            if (! $user->can($privileges)) {
                throw new RuntimeException('You have no permission for this');
            }
        }

        return $user;
    }

}