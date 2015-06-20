<?php

namespace Pop\Console\Test;

use Pop\Console\Input\Option;

class OptionTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $option = new Option('-h');
        $this->assertInstanceOf('Pop\Console\Input\Option', $option);
    }

    public function testSetNameBadNameException1()
    {
        $this->setExpectedException('InvalidArgumentException');
        $option = new Option('badname|b');
    }

    public function testSetNameBadNameException2()
    {
        $this->setExpectedException('InvalidArgumentException');
        $option = new Option('badname');
    }

}