<?php

namespace BaseTest;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class ErrorTest extends AbstractHttpControllerTestCase
{

    public function setUp()
    {
        $this->setApplicationConfig(include getcwd() . '/config/application.config.php');

        parent::setUp();
    }

    public function testErrorPage()
    {
        $this->dispatch('/balrog');
        $this->assertResponseStatusCode(404);
    }

}