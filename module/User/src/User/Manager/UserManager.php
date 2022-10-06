<?php

namespace User\Manager;

use Base\Manager\AbstractManager;
use Booking\Entity\Booking;
use Exception;
use InvalidArgumentException;
use RuntimeException;
use User\Entity\User;
use User\Entity\UserFactory;
use User\Table\UserMetaTable;
use User\Table\UserTable;
use Zend\Crypt\Password\Bcrypt;
use Zend\Db\Sql\Predicate\In;
use Zend\Db\Sql\Predicate\Like;
use Zend\Db\Sql\Predicate\NotIn;

class UserManager extends AbstractManager
{

    protected $userTable;
    protected $userMetaTable;

    /**
     * Creates a new user manager object.
     *
     * @param UserTable $userTable
     * @param UserMetaTable $userMetaTable
     */
    public function __construct(UserTable $userTable, UserMetaTable $userMetaTable)
    {
        $this->userTable = $userTable;
        $this->userMetaTable = $userMetaTable;
    }

    /**
     * Creates a new user.
     *
     * @param string $alias
     * @param string $status
     * @param string $email
     * @param string $pw
     * @param array $meta
     * @return User
     */
    public function create($alias, $status = 'placeholder', $email = null, $pw = null, array $meta = array())
    {
        if (! (is_string($alias) && strlen($alias) >= 3)) {
            throw new InvalidArgumentException('User name too short');
        }

        $bcrypt = new Bcrypt();
        $bcrypt->setCost(6);

        $user = new User(array(
            'alias' => $alias,
            'status' => $status,
            'email' => $email,
            'pw' => $bcrypt->create($pw),
        ), $meta);

        $this->save($user);

        $this->getEventManager()->trigger('create', $user);

        return $user;
    }

    /**
     * Saves (updates or creates) a user.
     *
     * @param User $user
     * @throws Exception
     * @return User
     */
    public function save(User $user)
    {
        $connection = $this->userTable->getAdapter()->getDriver()->getConnection();

        if (! $connection->inTransaction()) {
            $connection->beginTransaction();
            $transaction = true;
        } else {
            $transaction = false;
        }

        try {

            if ($user->get('uid')) {

                /* Update existing user */

                /* Determine updated properties */

                $updates = array();

                foreach ($user->need('updatedProperties') as $property) {
                    $updates[$property] = $user->get($property);
                }

                if ($updates) {
                    $this->userTable->update($updates, array('uid' => $user->get('uid')));
                }

                /* Determine new meta properties */

                foreach ($user->need('insertedMetaProperties') as $metaProperty) {
                    $this->userMetaTable->insert(array(
                        'uid' => $user->get('uid'),
                        'key' => $metaProperty,
                        'value' => $user->needMeta($metaProperty),
                    ));
                }

                /* Determine updated meta properties */

                foreach ($user->need('updatedMetaProperties') as $metaProperty) {
                    $this->userMetaTable->update(array(
                        'value' => $user->needMeta($metaProperty),
                    ), array('uid' => $user->get('uid'), 'key' => $metaProperty));
                }

                /* Determine removed meta properties */

                foreach ($user->need('removedMetaProperties') as $metaProperty) {
                    $this->userMetaTable->delete(array('uid' => $user->get('uid'), 'key' => $metaProperty));
                }

                $user->reset();

                $this->getEventManager()->trigger('save.update', $user);

            } else {

                /* Insert user */

                $created = date('Y-m-d H:i:s');

                if ($user->getExtra('nuid')) {
                    $uid = $user->getExtra('nuid');
                } else {
                    $uid = null;
                }

                $this->userTable->insert(array(
                    'uid' => $uid,
                    'alias' => $user->need('alias'),
                    'status' => $user->need('status'),
                    'email' => $user->get('email'),
                    'pw' => $user->get('pw'),
                    'login_attempts' => $user->get('login_attempts'),
                    'login_detent' => $user->get('login_detent'),
                    'last_activity' => $user->get('last_activity'),
                    'last_ip' => $user->get('last_ip'),
                    'created' => $user->get('created', $created),
                ));

                $uid = $this->userTable->getLastInsertValue();

                if (! (is_numeric($uid) && $uid > 0)) {
                    throw new RuntimeException('Failed to save user');
                }

                foreach ($user->need('meta') as $key => $value) {
                    $this->userMetaTable->insert(array(
                        'uid' => $uid,
                        'key' => $key,
                        'value' => $value,
                    ));

                    if (! $this->userMetaTable->getLastInsertValue()) {
                        throw new RuntimeException( sprintf('Failed to save user meta key "%s"', $key) );
                    }
                }

                $user->add('uid', $uid);

                if (! $user->get('created')) {
                    $user->add('created', $created);
                }

                $this->getEventManager()->trigger('save.insert', $user);
            }

            if ($transaction) {
                $connection->commit();
                $transaction = false;
            }

            $this->getEventManager()->trigger('save', $user);

            return $user;

        } catch (Exception $e) {
            if ($transaction) {
                $connection->rollback();
            }

            throw $e;
        }
    }

    /**
     * Gets the user by primary id.
     *
     * @param int $uid
     * @param boolean $strict
     * @return User
     * @throws RuntimeException
     */
    public function get($uid, $strict = true)
    {
        $users = $this->getBy(array('uid' => $uid));

        if (empty($users)) {
            if ($strict) {
                throw new RuntimeException('This user does not exist');
            }

            return null;
        } else {
            return $this->buffer[$uid] = current($users);
        }
    }

    /**
     * Gets all users that match the passed conditions.
     *
     * @param mixed $where              Any valid where conditions, but usually an array with key/value pairs.
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @param boolean $loadMeta
     * @return array
     */
    public function getBy($where, $order = null, $limit = null, $offset = null, $loadMeta = true)
    {
        $select = $this->userTable->getSql()->select();

        if ($where) {
            $select->where($where);
        }

        if ($order) {
            $select->order($order);
        }

        if ($limit) {
            $select->limit($limit);

            if ($offset) {
                $select->offset($offset);
            }
        }

        $resultSet = $this->userTable->selectWith($select);

        $users = UserFactory::fromResultSet($resultSet);

        if (! ($users && $loadMeta)) {
            return $users;
        }

        /* Load user meta data */

        $uids = array();

        foreach ($users as $user) {
            $uids[] = $user->need('uid');
        }

        reset($users);

        $metaSelect = $this->userMetaTable->getSql()->select();
        $metaSelect->where(new In('uid', $uids));

        $metaResultSet = $this->userMetaTable->selectWith($metaSelect);

        return UserFactory::fromMetaResultSet($users, $metaResultSet);
    }

    /**
     * Gets users by bookings.
     *
     * Users will be added to the bookings under the extra key 'user'.
     *
     * @param array $bookings
     * @return array
     * @throws InvalidArgumentException
     */
    public function getByBookings(array $bookings)
    {
        if (empty($bookings)) {
            return array();
        }

        $uids = array();

        foreach ($bookings as $booking) {
            if (! ($booking instanceof Booking)) {
                throw new InvalidArgumentException('Booking objects required to load from');
            }

            $uid = $booking->need('uid');

            if (! in_array($uid, $uids)) {
                $uids[] = $uid;
            }
        }

        $users = $this->getBy(new In(UserTable::NAME . '.uid', $uids));

        foreach ($bookings as $booking) {
            $booking->setExtra('user', $users[$booking->need('uid')]);
        }

        return $users;
    }

    public function getByPhoneNumber($number)
    {
        $resultSet = $this->userMetaTable->select(['key' => 'phone', 'value' => $number]);

        foreach ($resultSet as $resultRecord) {
            return $this->get($resultRecord->uid);
        }

        return null;
    }

    /**
     * Gets all users.
     *
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @param boolean $loadMeta
     * @return array
     */
    public function getAll($order = null, $limit = null, $offset = null, $loadMeta = true)
    {
        return $this->getBy(null, $order, $limit, $offset, $loadMeta);
    }

    /**
     * Interprets the input to return matching users.
     *
     * @param int|string $input     Any input for interpretation;
     *                              numeric or at least three chars long
     * @param int $limit            Maximum number of users to return
     * @param boolean $loadMeta     Whether to also load meta data
     * @param array $where          Additional where clauses
     * @return array                An array of matching user objects;
     *                              empty if invalid input or no results
     * @throws InvalidArgumentException
     */
    public function interpret($input, $limit = null, $loadMeta = false, array $where = array())
    {
        if (! (is_numeric($input) || is_string($input))) {
            throw new InvalidArgumentException('User interpretation requires either numeric or string input');
        }

        if (! is_numeric($input) && is_string($input) && strlen($input) < 3) {
            return array();
        }

        if (is_numeric($input)) {
            $user = $this->get($input, false);

            if ($user) {
                return array($user->need('uid') => $user);
            } else {
                return array();
            }
        } else {
            if (empty($where)) {
                $where = array(
                    new NotIn('status', array('deleted', 'disabled')),
                );
            }

            return $this->getBy(array_merge(array(new Like('alias', '%' . $input . '%')), $where), 'alias ASC', $limit, null, $loadMeta);
        }
    }

    /**
     * Deletes one user and all respective meta properties (through database foreign keys).
     *
     * @param int|User $user
     * @return int
     * @throws InvalidArgumentException
     */
    public function delete($user)
    {
        if ($user instanceof User) {
            $uid = $user->need('uid');
        } else {
            $uid = $user;
        }

        if (! (is_numeric($uid) && $uid > 0)) {
            throw new InvalidArgumentException('User id must be numeric');
        }

        $user = $this->get($uid);

        $deletion = $this->userTable->delete(array('uid' => $uid));

        $this->getEventManager()->trigger('delete', $user);

        return $deletion;
    }

}
