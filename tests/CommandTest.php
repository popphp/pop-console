<?php

namespace Pop\Console\Test;

use Pop\Application;
use Pop\Console\Command;
use Pop\Console\Console;
use PHPUnit\Framework\TestCase;

class CommandTest extends TestCase
{

    public function testConstructor()
    {
        $command = new Command(name: 'edit');
        $this->assertInstanceOf('Pop\Console\Command', $command);
    }

    public function testImplementsCommandInterface()
    {
        $command = new Command(name: 'edit');
        $this->assertInstanceOf('Pop\Console\Command\AbstractCommand', $command);
        $this->assertInstanceOf('Pop\Console\Command\CommandInterface', $command);
        $this->assertInstanceOf('Pop\Dispatch\DispatchableInterface', $command);
    }

    public function testSetAndGetName()
    {
        $command = new Command(name: 'hello');
        $command->setName('helloworld');
        $this->assertEquals('helloworld', $command->getName());
    }

    public function testSetAndGetParams()
    {
        $command = new Command(name: 'hello');
        $command->setParams('-v');
        $this->assertTrue($command->hasParams());
        $this->assertEquals('-v', $command->getParams());
    }

    public function testSetAndGetHelp()
    {
        $command = new Command(name: 'hello');
        $command->setHelp('Hello World');
        $this->assertTrue($command->hasHelp());
        $this->assertEquals('Hello World', $command->getHelp());
    }

    public function testToString()
    {
        $command = new Command(name: 'hello', params: '-v');
        $this->assertEquals('hello -v', (string)$command);
    }

    public function testConstructorSetsApplicationAndConsole()
    {
        $application = new Application();
        $console     = new Console(80);
        $command     = new Command($application, $console, 'hello', '-v', 'Hello World');

        $this->assertSame($application, $command->getApplication());
        $this->assertSame($console, $command->getConsole());
        $this->assertTrue($command->hasApplication());
        $this->assertTrue($command->hasConsole());
    }

    public function testConsoleDefaultsToNewInstanceWhenNotProvided()
    {
        $command = new Command(name: 'hello');
        $this->assertTrue($command->hasConsole());
        $this->assertInstanceOf('Pop\Console\Console', $command->getConsole());
        $this->assertFalse($command->hasApplication());
    }

    public function testSetAndGetApplication()
    {
        $command = new Command(name: 'hello');
        $this->assertFalse($command->hasApplication());
        $command->setApplication(new Application());
        $this->assertTrue($command->hasApplication());
        $this->assertInstanceOf('Pop\Application', $command->getApplication());
        $this->assertInstanceOf('Pop\Application', $command->application());
    }

    public function testSetAndGetConsole()
    {
        $command = new Command(name: 'hello');
        $console  = new Console(80);
        $command->setConsole($console);
        $this->assertTrue($command->hasConsole());
        $this->assertSame($console, $command->getConsole());
        $this->assertInstanceOf('Pop\Console\Console', $command->console());
    }

    public function testDispatchWithoutHandleMethodThrowsException()
    {
        $this->expectException('Pop\Dispatch\Exception');

        $command = new Command(name: 'hello');
        $command->dispatch();
    }

}
