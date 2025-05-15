<?php

namespace User\Entity;

use Base\Entity\AbstractEntity;

class User extends AbstractEntity
{

    protected $uid;
    protected $alias;
    protected $status;
    protected $email;
    protected $pw;
    protected $login_attempts;
    protected $login_detent;
    protected $last_activity;
    protected $last_ip;
    protected $created;

    /**
     * The possible status options.
     *
     * @var array
     */
    public static $statusOptions = array(
        'placeholder' => 'Placeholder',
        'deleted' => 'Deleted user',
        'blocked' => 'Blocked user',
        'disabled' => 'Waiting for activation',
        'enabled' => 'Enabled user',
        'assist' => 'Assist',
        'admin' => 'Admin',
    );

    /**
     * Returns the status string.
     *
     * @return string
     */
    public function getStatus()
    {
        $status = $this->need('status');

        return self::$statusOptions[$status] ?? 'Unknown';
    }

    /**
     * The possible gender options.
     *
     * @var array
     */
    public static $genderOptions = array(
        'male' => 'Mr.',
        'female' => 'Mrs',
        'family' => 'Family',
        'firm' => 'Firm',
    );

    /**
     * Returns the gender string.
     *
     * @return string
     */
    public function getGender($default = null)
    {
        $gender = $this->getMeta('gender');

        if (is_null($gender)) {
            return $default;
        }

        return self::$genderOptions[$gender] ?? 'Unknown';
    }

    /**
     * The possible privileges.
     *
     * @var array
     */
    public static $privileges = array(
        'admin.user' => 'May manage users',
        'admin.booking' => 'May manage bookings',
        'admin.event' => 'May manage events',
        'admin.config' => 'May change configuration',
        'admin.see-menu' => 'Can see the admin menu',
        'calendar.see-past' => 'Can see the past in calendar',
        'calendar.see-data' => 'Can see names and data in calendar',
        'calendar.create-single-bookings' => 'May create single bookings',
        'calendar.cancel-single-bookings' => 'May cancel single bookings',
        'calendar.delete-single-bookings' => 'May delete single bookings',
        'calendar.create-subscription-bookings' => 'May create multiple bookings',
        'calendar.cancel-subscription-bookings' => 'May cancel multiple bookings',
        'calendar.delete-subscription-bookings' => 'May delete multiple bookings',
    );

    /**
     * Access control for this user.
     *
     * @param string $privileges
     * @return boolean
     */
    public function can($privileges)
    {
        if ($this->need('status') == 'admin') {
            return true;
        }

        if ($this->need('status') == 'assist') {
            if (is_array($privileges)) {
                $privileges = implode(',', $privileges);
            }

            if (is_string($privileges)) {
                $orPrivileges = explode(',', $privileges);
                $orPrivilegesMatched = 0;

                foreach ($orPrivileges as $orPrivilege) {
                    $andPrivileges = explode('+', $orPrivilege);
                    $andPrivilegesMatched = 0;

                    foreach ($andPrivileges as $andPrivilege) {
                        $privilege = trim($andPrivilege);

                        if ($this->getMeta('allow.' . $privilege) == 'true') {
                            $andPrivilegesMatched++;
                        }
                    }

                    if ($andPrivilegesMatched == count($andPrivileges)) {
                        $orPrivilegesMatched++;
                    }
                }

                if ($orPrivilegesMatched >= 1) {
                    return true;
                }
            }
        }

        return false;
    }

}
