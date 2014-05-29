<?php

namespace Setup\Controller\Plugin;

use Base\Manager\OptionManager;
use User\Manager\UserManager;
use Zend\Db\Adapter\Adapter;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class ValidateSetup extends AbstractPlugin
{

    protected $optionManager;
    protected $userManager;
    protected $dbAdapter;

    public function __construct(OptionManager $optionManager, UserManager $userManager, Adapter $dbAdapter)
    {
        $this->optionManager = $optionManager;
        $this->userManager = $userManager;
        $this->dbAdapter = $dbAdapter;
    }

    public function __invoke($action)
    {
        switch ($action) {
            case 'index':
            case 'tables':
                $res = $this->dbAdapter->query('SHOW TABLES', Adapter::QUERY_MODE_EXECUTE)->toArray();

                if ($res && count($res) > 0) {
                    throw new \RuntimeException('System has already been setup');
                }

                break;
            case 'records':
                if ($this->optionManager->get('client.name.full')) {
                    throw new \RuntimeException('System has already been setup');
                }

                break;
            case 'user':
                $users = $this->userManager->getAll(null, 1);

                if ($users && count($users) > 0) {
                    throw new \RuntimeException('System has already been setup');
                }

                break;
        }
    }

}