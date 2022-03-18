<?php

namespace UserTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class AccountControllerTest extends AbstractHttpControllerTestCase
{

    public function setUp()
    {
        $this->setApplicationConfig(require 'config/application.config.php');

        parent::setUp();
    }

    public function testRegistrationActionCanBeAccessed()
    {
        $this->dispatch('/user/registration');

        $this->assertResponseStatusCode(200);

        $this->assertModuleName('User');
        $this->assertControllerName('User\Controller\Account');
        $this->assertControllerClass('AccountController');
        $this->assertMatchedRouteName('user/registration');

        $this->assertQuery('#rf-email1');
        $this->assertQuery('#rf-email2');
        $this->assertQuery('#rf-pw1');
        $this->assertQuery('#rf-pw2');
    }

    public function testActivationActionCannotBeAccessed()
    {
        $this->dispatch('/user/activation');

        $this->assertResponseStatusCode(500);
    }

    public function testBookingsActionCannotBeAccessed()
    {
        $this->dispatch('/user/bookings');

        $this->assertResponseStatusCode(500);
    }

    public function testSettingsActionCannotBeAccessed()
    {
        $this->dispatch('/user/settings');

        $this->assertResponseStatusCode(500);
    }

}