<?php

namespace Backend\Controller;

use User\Entity\User;
use User\Table\UserTable;
use Zend\Crypt\Password\Bcrypt;
use Zend\Db\Adapter\Adapter;
use Zend\Mvc\Controller\AbstractActionController;

class UserController extends AbstractActionController
{

    public function indexAction()
    {
        $this->authorize('admin.user');

        $serviceManager = @$this->getServiceLocator();
        $userManager = $serviceManager->get('User\Manager\UserManager');

        $users = array();

        $search = $this->params()->fromQuery('usf-search');

        if ($search) {
            $filters = $this->backendUserDetermineFilters($search);
            $filterFreeSearch = $filters['search'];

            try {
                $limit = 1000;

                if ($filterFreeSearch) {
                    if (preg_match('/\(([0-9]+)\)/', $filterFreeSearch, $matches)) {
                        $filterFreeSearch = $matches[1];
                    }

                    $users = $userManager->interpret($filterFreeSearch, $limit, true, $filters['filters']);
                } else {
                    $users = $userManager->getBy($filters['filters'], 'alias ASC', $limit);
                }
            } catch (\RuntimeException $e) {
                $users = array();
            }
        }

        return array(
            'search' => $search,
            'users' => $users,
        );
    }

    public function editAction()
    {
        $sessionUser = $this->authorize('admin.user');

        $serviceManager = @$this->getServiceLocator();
        $userManager = $serviceManager->get('User\Manager\UserManager');
        $formElementManager = $serviceManager->get('FormElementManager');

        $uid = $this->params()->fromRoute('uid');
        $search = $this->params()->fromQuery('search');

        if ($uid) {
            $user = $userManager->get($uid);
        } else {
            $user = null;
        }

        $editUserForm = $formElementManager->get('Backend\Form\User\EditForm');

        if ($this->getRequest()->isPost()) {
            $editUserForm->setData($this->params()->fromPost());

            if ($editUserForm->isValid()) {
                $eud = $editUserForm->getData();

                if (! $user) {
                    $user = new User();
                }

                if ($user->get('status') == 'admin') {
                    if (! $sessionUser->can('admin')) {
                        $this->flashMessenger()->addInfoMessage('Admin users can only be edited by admins');

                        return $this->redirect()->toRoute('backend/user/edit', ['uid' => $uid]);
                    }
                }

                /* Account data */

                $user->set('alias', $eud['euf-alias']);

                $status = $eud['euf-status'];

                if ($status == 'admin') {
                    if ($sessionUser->can('admin')) {
                        $user->set('status', $status);
                    } else {
                        $this->flashMessenger()->addInfoMessage('Admin status can only be given by admins');

                        if (! $user->get('uid')) {
                            return $this->redirect()->toRoute('backend/user/edit', ['uid' => $uid]);
                        }
                    }
                } else {
                    $user->set('status', $status);
                }

                if ($eud['euf-privileges']) {
                    if ($sessionUser->can('admin')) {
                        foreach (User::$privileges as $privilege => $privilegeLabel) {
                            if (in_array($privilege, $eud['euf-privileges'])) {
                                $user->setMeta('allow.' . $privilege, 'true');
                            } else {
                                $user->setMeta('allow.' . $privilege, null);
                            }
                        }
                    } else {
                        $this->flashMessenger()->addInfoMessage('Privileges can only be edited by admins');
                    }
                }

                $user->set('email', $eud['euf-email']);

                $pw = $eud['euf-pw'];

                if ($pw) {
                    $bcrypt = new Bcrypt();
                    $bcrypt->setCost(6);

                    $user->set('pw', $bcrypt->create($pw));
                }

                /* Personal data */

                $user->setMeta('gender', $eud['euf-gender']);

                switch ($eud['euf-gender']) {
                    case 'family':
                    case 'firm':
                        $user->setMeta('name', $eud['euf-firstname']);
                        break;
                    default:
                        $user->setMeta('firstname', $eud['euf-firstname']);
                        $user->setMeta('lastname', $eud['euf-lastname']);
                }

                $user->setMeta('street', $eud['euf-street']);
                $user->setMeta('zip', $eud['euf-zip']);
                $user->setMeta('city', $eud['euf-city']);
                $user->setMeta('phone', $eud['euf-phone']);
                $user->setMeta('birthdate', $eud['euf-birthdate']);
                $user->setMeta('notes', $eud['euf-notes']);

                $userManager->save($user);

                $this->flashMessenger()->addSuccessMessage('User has been saved');

                if ($search) {
                    return $this->redirect()->toRoute('backend/user', [], ['query' => ['usf-search' => $search]]);
                } else {
                    return $this->redirect()->toRoute('frontend');
                }
            }
        } else {
            if ($user) {
                $privileges = array();

                foreach (User::$privileges as $privilege => $privilegeLabel) {
                    if ($user->getMeta('allow.' . $privilege) == 'true') {
                        $privileges[] = $privilege;
                    }
                }

                $editUserForm->setData(array(
                    'euf-uid' => $user->need('uid'),
                    'euf-alias' => $user->need('alias'),
                    'euf-status' => $user->need('status'),
                    'euf-privileges' => $privileges,
                    'euf-email' => $user->get('email'),
                    'euf-gender' => $user->getMeta('gender'),
                    'euf-firstname' => $user->getMeta('firstname', $user->getMeta('name')),
                    'euf-lastname' => $user->getMeta('lastname'),
                    'euf-street' => $user->getMeta('street'),
                    'euf-zip' => $user->getMeta('zip'),
                    'euf-city' => $user->getMeta('city'),
                    'euf-phone' => $user->getMeta('phone'),
                    'euf-birthdate' => $user->getMeta('birthdate'),
                    'euf-notes' => $user->getMeta('notes'),
                ));
            }
        }

        return array(
            'editUserForm' => $editUserForm,
            'user' => $user,
            'search' => $search,
        );
    }

    public function deleteAction()
    {
        $this->authorize('admin.user');

        $uid = $this->params()->fromRoute('uid');
        $search = $this->params()->fromQuery('search');

        $serviceManager = @$this->getServiceLocator();
        $bookingManager = $serviceManager->get('Booking\Manager\BookingManager');
        $userManager = $serviceManager->get('User\Manager\UserManager');

        $user = $userManager->get($uid);
        $bookings = $bookingManager->getBy(['uid' => $uid]);

        if ($this->params()->fromQuery('confirmed') == 'true') {

            if ($bookings) {

                // User already has bookings, so we can only set his status to disabled
                $user->set('status', 'deleted');
                $userManager->save($user);

                $this->flashMessenger()->addSuccessMessage('User status has been set to deleted');
            } else {

                // User has no bookings, so we can actually delete him
                $userManager->delete($user);

                $this->flashMessenger()->addSuccessMessage('User has been deleted');
            }

            if ($search) {
                return $this->redirect()->toRoute('backend/user', [], ['query' => ['usf-search' => $search]]);
            } else {
                return $this->redirect()->toRoute('frontend');
            }
        }

        return array(
            'uid' => $uid,
            'search' => $search,
            'user' => $user,
            'bookings' => $bookings,
        );
    }

    public function interpretAction()
    {
        $this->authorize('admin.user, admin.booking, calendar.see-data');

        $serviceManager = @$this->getServiceLocator();
        $userManager = $serviceManager->get('User\Manager\UserManager');

        $term = $this->params()->fromQuery('term');

        $usersMax = 15;

        $users = $userManager->interpret($term, $usersMax);

        $usersList = array();

        foreach ($users as $uid => $user) {
            $usersList[] = sprintf('%s (%s)', $user->need('alias'), $uid);
        }

        if (count($usersList) == $usersMax) {
            $usersList[] = '[...]';
        }

        return $this->jsonViewModel($usersList);
    }

    public function statsAction()
    {
        $this->authorize('admin.user');

        $db = @$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');

        $stats = $db->query(sprintf('SELECT status, COUNT(status) AS count FROM %s GROUP BY status', UserTable::NAME),
            Adapter::QUERY_MODE_EXECUTE)->toArray();

        return array(
            'stats' => $stats,
        );
    }

}
