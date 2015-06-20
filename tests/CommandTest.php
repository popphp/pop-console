<?php

namespace Pop\Console\Test;

use Pop\Console\Input\Command;

class CommandTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $command = new Command('-h');
        $this->assertInstanceOf('Pop\Console\Input\Command', $command);
    }

    public function testSetAndGetHelp()
    {
        $command = new Command('-h');
        $command->setHelp('Hello World');
        $this->assertEquals('Hello World', $command->getHelp());
    }

    public function testSetValueModeOptional()
    {
        $command = new Command('-h');
        $command->setValueMode(Command::VALUE_OPTIONAL);
        $this->assertTrue($command->isValueOptional());
    }

    public function testSetValueModeRequiredArray()
    {
        $command = new Command('-h');
        $command->setValueMode(Command::VALUE_REQUIRED|Command::VALUE_IS_ARRAY);
        $this->assertTrue($command->isValueRequired());
        $this->assertTrue($command->isValueArray());
    }

    public function testSetValueModeOptionalArray()
    {
        $command = new Command('-h');
        $command->setValueMode(Command::VALUE_OPTIONAL|Command::VALUE_IS_ARRAY);
        $this->assertTrue($command->isValueOptional());
        $this->assertTrue($command->isValueArray());
        $this->assertTrue($command->acceptsValue());
    }

}