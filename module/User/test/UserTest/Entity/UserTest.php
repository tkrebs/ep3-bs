<?php

namespace UserTest\Entity;

use User\Entity\User;

class UserTest extends \PHPUnit_Framework_TestCase
{

    public function testAbstractEntityConstructor()
    {
        $user = new User(array(
            'uid' => 1,
            'alias' => 'Someone',
            'status' => 'enabled',
        ), array(
            'gender' => 'unknown',
        ));

        return $user;
    }

    /**
     * @depends testAbstractEntityConstructor
     */
    public function testAbstractEntityGetNeed(User $user)
    {
        $this->assertEquals(1, $user->get('uid'));
        $this->assertEquals('Someone', $user->get('alias'));
        $this->assertEquals('enabled', $user->need('status'));

        $this->assertNull($user->get('created'));
        $this->assertEquals('now', $user->get('created', 'now'));

        $this->assertEquals(1, $user->get('uid', null, 'numeric'));
        $this->assertEquals('Someone', $user->get('alias', null, 'string'));
    }

    /**
     * @depends testAbstractEntityConstructor
     * @expectedException \RuntimeException
     */
    public function testAbstractEntityGetNeedFailure(User $user)
    {
        $user->need('invalid');
    }

    /**
     * @depends testAbstractEntityConstructor
     */
    public function testAbstractEntitySet(User $user)
    {
        $user->set('email', 'someone@example.com');

        $this->assertEquals('someone@example.com', $user->get('email'));

        $user->set('email', null);

        $this->assertNull($user->get('email'));

        $user->set('invalid', 'something', false);

        $this->assertNull($user->get('invalid'));
    }

    /**
     * @depends testAbstractEntityConstructor
     * @expectedException \InvalidArgumentException
     */
    public function testAbstractEntitySetFailure(User $user)
    {
        $user->set('invalid', 'something');
    }

    /**
     * @depends testAbstractEntityConstructor
     */
    public function testAbstractEntityGetNeedMeta(User $user)
    {
        $this->assertEquals('unknown', $user->getMeta('gender'));

        $this->assertNull($user->getMeta('firstname'));

        $this->assertEquals('unknown', $user->getMeta('gender', null, 'string'));
    }

    /**
     * @depends testAbstractEntityConstructor
     * @expectedException \RuntimeException
     */
    public function testAbstractEntityGetNeedMetaFailure(User $user)
    {
        $user->needMeta('invalid');
    }

     /**
     * @depends testAbstractEntityConstructor
     */
    public function testAbstractEntitySetMeta(User $user)
    {
        $user->setMeta('locale', 'en-US');

        $this->assertEquals('en-US', $user->getMeta('locale'));

        $user->setMeta('locale', null);

        $this->assertNull($user->getMeta('locale'));
    }

    /**
     * @depends testAbstractEntityConstructor
     * @expectedException \InvalidArgumentException
     */
    public function testAbstractEntitySetMetaFailure(User $user)
    {
        $user->setMeta(['one', 'two'], 'three');
    }

}