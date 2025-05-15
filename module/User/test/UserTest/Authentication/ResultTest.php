<?php

namespace UserTest\Authentication;

use User\Authentication\Result;
use Zend\Authentication\Result as ResultAlias;

class ResultTest extends \PHPUnit_Framework_TestCase
{

    public function testAdditionalFailureCodes()
    {
        $result1 = new Result(Result::FAILURE_TOO_MANY_TRIES, 'Someone');
        $valid1 = $result1->isValid();

        $this->assertFalse($valid1);

        $result2 = new Result(Result::FAILURE_USER_STATUS, 'Someone');
        $valid2 = $result2->isValid();

        $this->assertFalse($valid2);
    }

    public function testExtraData()
    {
        $result = new Result(ResultAlias::SUCCESS, 'Someone');

        $result->setExtra('date', '1970-01-01');

        $this->assertEquals('1970-01-01', $result->getExtra('date'));

        $this->assertNull($result->getExtra('time'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExtraDataInvalidArrayKey()
    {
        $result = new Result(ResultAlias::SUCCESS, 'Someone');

        $result->setExtra(array(), 'Something');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExtraDataInvalidObjectKey()
    {
        $result = new Result(ResultAlias::SUCCESS, 'Someone');

        $result->setExtra(new \stdClass(), 'Something');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExtraDataInvalidNullKey()
    {
        $result = new Result(ResultAlias::SUCCESS, 'Someone');

        $result->setExtra(null, 'Something');
    }

}