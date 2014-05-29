<?php

namespace Setup\Controller\Plugin;

use Zend\Db\Adapter\Adapter;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\ServiceManager;

class ValidateSetup extends AbstractPlugin
{

    protected $serviceManager;
    protected $dbAdapter;

    public function __construct(ServiceManager $serviceManager, Adapter $dbAdapter)
    {
        $this->serviceManager = $serviceManager;
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
                $optionManager = $this->serviceManager->get('Base\Manager\OptionManager');

                if ($optionManager->get('client.name.full')) {
                    throw new \RuntimeException('System has already been setup');
                }

                break;
            case 'user':
                $userManager = $this->serviceManager->get('User\Manager\UserManager');

                $users = $userManager->getAll(null, 1);

                if ($users && count($users) > 0) {
                    throw new \RuntimeException('System has already been setup');
                }

                break;
        }
    }

}