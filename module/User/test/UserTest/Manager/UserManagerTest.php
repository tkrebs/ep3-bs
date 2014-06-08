<?php

namespace UserTest\Manager;

use User\Entity\User;
use User\Manager\UserManager;

class UserManagerTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $userTableMock = $this->getMockBuilder('User\Table\UserTable')
            ->disableOriginalConstructor()
            ->getMock();

        $userMetaTableMock = $this->getMockBuilder('User\Table\UserMetaTable')
            ->disableOriginalConstructor()
            ->getMock();

        $userManager = new UserManager($userTableMock, $userMetaTableMock);

        return $userManager;
    }

    /**
     * @depends testConstructor
     */
    public function testCreateCreatesUser(UserManager $userManager)
    {
        $userManager->create('Someone');
        $userManager->create('Someone', 'enabled');

        $user = $userManager->create('Someone', 'enabled', 'someone@example.com', 'something', array(
            'gender' => 'unknown',
        ));

        $this->assertTrue($user instanceof User);

        $this->assertEquals(array(
            'Someone',
            'enabled',
            'someone@example.com',
        ), array(
            $user->get('alias'),
            $user->get('status'),
            $user->get('email'),
        ));

        $this->assertNotEquals(array(
            'something',
        ), array(
            $user->get('pw'),
        ));
    }

    /**
     * @depends testConstructor
     * @exptectedException \InvalidArgumentException
     */
    public function testCreateFailure1(UserManager $userManager)
    {
        $userManager->create(5);
    }

    /**
     * @depends testConstructor
     * @exptectedException \InvalidArgumentException
     */
    public function testCreateFailure2(UserManager $userManager)
    {
        $userManager->create('s');
    }

}