<?php

namespace Pop\Console\Test;

use Pop\Console\Command;
use PHPUnit\Framework\TestCase;

class CommandTest extends TestCase
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

    public function testSetAndGetParams()
    {
        $command = new Command('hello');
        $command->setParams('-v');
        $this->assertTrue($command->hasParams());
        $this->assertEquals('-v', $command->getParams());
    }

    public function testSetAndGetHelp()
    {
        $command = new Command('hello');
        $command->setHelp('Hello World');
        $this->assertTrue($command->hasHelp());
        $this->assertEquals('Hello World', $command->getHelp());
    }

    public function testToString()
    {
        $command = new Command('hello', '-v');
        $this->assertEquals('hello -v', (string)$command);
    }

}