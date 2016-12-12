<?php

namespace Pop\Console\Test;

use Pop\Console\Command;

class CommandTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $command = new Command('edit');
        $this->assertInstanceOf('Pop\Console\Command', $command);
    }

    public function testSetAndGetName()
    {
        $command = new Command('hello');
        $command->setName('helloworld');
        $this->assertEquals('helloworld', $command->getName());
    }

    public function testSetAndGetHelp()
    {
        $command = new Command('hello');
        $command->setHelp('Hello World');
        $this->assertEquals('Hello World', $command->getHelp());
    }

}