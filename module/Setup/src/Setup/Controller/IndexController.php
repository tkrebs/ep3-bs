<?php

namespace Setup\Controller;

use Backend\Form\Config\TextForm;
use Setup\Form\UserForm;
use Square\Entity\Square;
use Zend\Mvc\Controller\AbstractActionController;

class IndexController extends AbstractActionController
{

    public function indexAction()
    {
        $this->validateSetup('index');
    }

    public function tablesAction()
    {
        $this->validateSetup('tables');

        $import = false;
        $importMessage = null;

        $sqlFile = getcwd() . '/data/db/ep3-bs.sql';

        if (is_readable($sqlFile)) {

            $sqlContent = file_get_contents($sqlFile);

            $dbAdapter = @$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            $db = $dbAdapter->getDriver()->getConnection()->getResource();

            if ($db instanceof \PDO) {
                $db->setAttribute(\PDO::ATTR_EMULATE_PREPARES, 0);

                try {
                    $db->exec($sqlContent);

                    $statement = $db->query('SHOW TABLES');
                    $statement->execute();

                    $res = $statement->fetchAll();

                    if (count($res) > 10) {
                        $import = true;
                    } else {
                        $import = false;
                    }
                } catch (\PDOException $e) {
                    $importMessage = $e->getMessage();
                }
            } else {
                $importMessage = 'Unsupported database adapter configured (PDO required)';
            }
        } else {
            $importMessage = 'SQL file <code>' . $sqlFile . '</code> not found';
        }

        return array(
            'import' => $import,
            'importMessage' => $importMessage,
        );
    }

    public function recordsAction()
    {
        $this->validateSetup('records');

        require 'module/Backend/src/Backend/Form/Config/TextForm.php';

        $textForm = new TextForm();
        $textForm->init();

        if ($this->getRequest()->isPost()) {
            $textForm->setData($this->params()->fromPost());

            if ($textForm->isValid()) {
                $textData = $textForm->getData();

                /* Setup options */

                $optionManager = @$this->getServiceLocator()->get('Base\Manager\OptionManager');

                foreach (TextForm::$definitions as $key => $value) {
                    $formKey = str_replace('.', '_', $key);
                    $formValue = $textData['cf-' . $formKey];

	                if (isset($value[2]) && $value[2]) {
				        $type = $value[2];
			        } else {
				        $type = 'Text';
			        }

	                if ($type == 'Checkbox') {
				        $formValue = (boolean) $formValue;
			        }

                    $optionManager->set($key, $formValue);
                }

                /* Setup default options */

                $uri = $this->getRequest()->getUri();
                $base = sprintf('%s://%s/', $uri->getScheme(), $uri->getHost());

                $optionManager->set('service.user.registration', 'true');
                $optionManager->set('service.user.activation', 'email');
                $optionManager->set('service.calendar.days', '4');
                $optionManager->set('service.website', $base);
                $optionManager->set('service.branding', 'true');
                $optionManager->set('service.branding.name', $this->t('ep-3 Bookingsystem'));
                $optionManager->set('service.branding.website', 'https://bs.hbsys.de/');

                /* Setup default squares */

                $squareManager = @$this->getServiceLocator()->get('Square\Manager\SquareManager');
                $squares = $squareManager->getAll();

                if (! $squares) {
                    $square1 = new Square(array(
                        'name' => 'A',
                        'status' => 'enabled',
                        'priority' => 1,
                        'capacity' => 1,
                        'capacity_heterogenic' => 0,
                        'time_start' => '08:00:00',
                        'time_end' => '22:00:00',
                        'time_block' => 3600,
                        'time_block_bookable' => 1800,
                        'time_block_bookable_max' => 10800,
                        'min_range_book' => 0,
                        'range_book' => 4838400,
                        'max_active_bookings' => 0,
                        'range_cancel' => 86400,
                    ));

                    $square2 = clone $square1;
                    $square2->set('name', 'B');
                    $square2->set('priority', 2);

                    $square3 = clone $square2;
                    $square3->set('name', 'C');
                    $square3->set('priority', 3);

                    $squareManager->save($square1);
                    $squareManager->save($square2);
                    $squareManager->save($square3);
                }

                return $this->redirect()->toRoute('setup/user');
            }
        } else {
            $textForm->setData(array(
                'cf-service_name_full' => $this->t('Bookingsystem'),
                'cf-service_name_short' => 'BS',
                'cf-subject_square_type' => $this->t('Square'),
                'cf-subject_square_type_plural' => $this->t('Squares'),
                'cf-subject_square_unit' => $this->t('Player'),
                'cf-subject_square_unit_plural' => $this->t('Players'),
                'cf-subject_type' => $this->t('our Facility'),
            ));
        }

        return array(
            'textForm' => $textForm,
        );
    }

    public function userAction()
    {
        $this->validateSetup('user');

        $userForm = new UserForm();
        $userForm->init();

        if ($this->getRequest()->isPost()) {
            $userForm->setData($this->params()->fromPost());

            if ($userForm->isValid()) {
                $userData = $userForm->getData();

                $firstname = $userData['uf-firstname'];
                $lastname = $userData['uf-lastname'];
                $email = $userData['uf-email'];
                $pw = $userData['uf-pw'];

                $alias = sprintf('%s %s',
                    $firstname,
                    $lastname);

                $userManager = @$this->getServiceLocator()->get('User\Manager\UserManager');

                $user = $userManager->create($alias, 'admin', $email, $pw);

                if ($user) {
                    return $this->redirect()->toRoute('setup/complete');
                }
            }
        } else {
            $userForm->setData(array(
                'uf-email' => $this->option('client.contact.email'),
            ));
        }

        return array(
            'userForm' => $userForm,
        );
    }

    public function completeAction()
    { }

}
