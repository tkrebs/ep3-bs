<?php

namespace User\Manager;

use Base\Manager\AbstractManager;
use Base\Manager\ConfigManager;
use DateTime;
use User\Authentication\Result;
use User\Entity\User;
use Zend\Authentication\Result as ResultAlias;
use Zend\Crypt\Password\Bcrypt;
use Zend\Session\Container;
use Zend\Session\SessionManager;
use Zend\Session\Validator\HttpUserAgent;
use Zend\Session\Validator\RemoteAddr;

class UserSessionManager extends AbstractManager
{

    protected $configManager;
    protected $userManager;
    protected $sessionManager;

    protected $sessionContainer;

    protected $attemptsAllowed = 5;
    protected $detentDuration = 600;

    protected $user;

    /**
     * Creates a new user session manager object.
     *
     * @param ConfigManager $configManager
     * @param UserManager $userManager
     * @param SessionManager $sessionManager;
     */
    public function __construct(ConfigManager $configManager, UserManager $userManager, SessionManager $sessionManager)
    {
        $this->configManager = $configManager;
        $this->userManager = $userManager;
        $this->sessionManager = $sessionManager;

        /* Prepare session validators */

        $sessionManager->getValidatorChain()->attach('session.validate', array(new HttpUserAgent(), 'isValid'));
        $sessionManager->getValidatorChain()->attach('session.validate', array(new RemoteAddr(), 'isValid'));
    }

    /**
     * Sets the user session container.
     *
     * @param Container $sessionContainer
     */
    public function setSessionContainer(Container $sessionContainer)
    {
        $this->sessionContainer = $sessionContainer;
    }

    /**
     * Gets the user session container.
     *
     * @return Container
     */
    public function getSessionContainer($namespace = 'UserSession')
    {
        if (! $this->sessionContainer) {
            $this->setSessionContainer(new Container($namespace));
        }

        return $this->sessionContainer;
    }

    /**
     * Gets the current session user.
     *
     * @return User|null
     */
    public function getSessionUser()
    {
        if ($this->user) {
            return $this->user;
        } else {
            $sessionName = $this->configManager->need('session_config.name');

            if (isset($_COOKIE[$sessionName])) {
                $container = $this->getSessionContainer();

                if (isset($container->uid) && is_numeric($container->uid) && $container->uid > 0) {
                    return $this->user = $this->userManager->get($container->uid, false);
                }
            }
        }

        return null;
    }

    /**
     * Creates the session for the user with the passed credentials.
     *
     * @param string $email
     * @param string $pw
     * @return Result
     */
    public function login($email, $pw)
    {
        $users = $this->userManager->getBy(array(
            'email' => $email,
        ));

        if (count($users) == 0) {
            return new Result(ResultAlias::FAILURE_IDENTITY_NOT_FOUND, $email);
        }

        if (count($users) >= 2) {
            return new Result(ResultAlias::FAILURE_IDENTITY_AMBIGUOUS, $email);
        }

        $user = current($users);

        /* Check for current login detent */

        $currentDateTime = new DateTime();

        if ($user->get('login_detent')) {
            $loginDetent = new DateTime($user->get('login_detent'));

            if ($loginDetent > $currentDateTime) {
                $result = new Result(Result::FAILURE_TOO_MANY_TRIES, $user);
                $result->setExtra('login_detent', $loginDetent);
                return $result;
            }
        }

        $bcrypt = new Bcrypt();
        $bcrypt->setCost(6);

        /* If legacy password is detected, use it for login and then delete it */

        if ($user->getMeta('legacy-pw')) {
            $legacyPw = $user->getMeta('legacy-pw');

            if ($legacyPw == md5($pw)) {
                $user->set('pw', $bcrypt->create($pw));
                $user->setMeta('legacy-pw', null);
            }
        }

        /* Check original credentials */

        if ($bcrypt->verify($pw, $user->need('pw'))) {

            /* Check user status */

            switch ($user->need('status')) {
                case 'placeholder':
                case 'deleted':
                case 'blocked':
                case 'disabled':
                    return new Result(Result::FAILURE_USER_STATUS, $user);
            }

            /* Create the session */

            $container = $this->getSessionContainer();
            $container->uid = $user->need('uid');
	        $container->status = $user->need('status');

            /* Update last activity and ip */

            $user->set('login_attempts', null);
            $user->set('login_detent', null);
            $user->set('last_activity', date('Y-m-d H:i:s'));
            $user->set('last_ip', $_SERVER['REMOTE_ADDR']);

            $this->userManager->save($user);

            /* Inform anyone interested in this */

            $this->getEventManager()->trigger('login', $user);

            return new Result(ResultAlias::SUCCESS, $user);
        }

        /* Invalid password passed, prepare detent */

        $loginAttempts = $user->get('login_attempts');

        if (! $loginAttempts) {
            $loginAttempts = 0;
        }

        $loginAttempts++;

        if ($loginAttempts >= $this->attemptsAllowed) {
            $loginAttempts = null;
            $loginDetent = clone $currentDateTime;
            $loginDetent->modify( sprintf('+%u sec', $this->detentDuration) );
        } else {
            $loginDetent = null;
        }

        $user->set('login_attempts', $loginAttempts);
        $user->set('login_detent', $loginDetent?->format('Y-m-d H:i:s'));

        $this->userManager->save($user);

        return new Result(ResultAlias::FAILURE_CREDENTIAL_INVALID, $user);
    }

    /**
     * Deletes the session from the current session user.
     *
     * @return boolean
     */
    public function logout()
    {
        $user = $this->getSessionUser();

        if (! $user) {
            return false;
        }

        /* Update last activity and ip */

        $user->set('last_activity', date('Y-m-d H:i:s'));
        $user->set('last_ip', $_SERVER['REMOTE_ADDR']);

        $this->userManager->save($user);

        /* Bye ... */

        $container = $this->getSessionContainer();
        $container->uid = null;
	    $container->status = null;

        $this->sessionManager->destroy();

        $this->user = null;

        /* Oh wait, inform anyone interested in this */

        $this->getEventManager()->trigger('logout', $user);

        return true;
    }

}
