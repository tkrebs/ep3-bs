<?php

namespace UserTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class SessionControllerTest extends AbstractHttpControllerTestCase
{

    public function setUp()
    {
        $this->setApplicationConfig(require 'config/application.config.php');

        parent::setUp();
    }

    public function testLoginActionCanBeAccessed()
    {
        $this->dispatch('/user/login');

        $this->assertResponseStatusCode(200);

        $this->assertModuleName('User');
        $this->assertControllerName('User\Controller\Session');
        $this->assertControllerClass('SessionController');
        $this->assertMatchedRouteName('user/login');

        $this->assertQuery('#lf-email');
        $this->assertQuery('#lf-pw');
    }

    public function testLogoutActionCanBeAccessed()
    {
        $this->dispatch('/user/logout');

        $this->assertResponseStatusCode(200);

        $this->assertModuleName('User');
        $this->assertControllerName('User\Controller\Session');
        $this->assertControllerClass('SessionController');
        $this->assertMatchedRouteName('user/logout');

        $this->assertQuery('.info-message');
    }

}