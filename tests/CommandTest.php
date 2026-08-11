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
        $command = new Command('edit');
        $this->assertInstanceOf('Pop\Console\Command', $command);
    }

    public function testImplementsCommandInterface()
    {
        $command = new Command('edit');
        $this->assertInstanceOf('Pop\Console\Command\AbstractCommand', $command);
        $this->assertInstanceOf('Pop\Console\Command\CommandInterface', $command);
        $this->assertInstanceOf('Pop\Dispatch\DispatchableInterface', $command);
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

    public function testConstructorWithApplicationAndConsole()
    {
        $application = new Application();
        $console     = new Console(80);
        $command     = new Command('hello', '-v', 'Hello World', $application, $console);

        $this->assertSame($application, $command->getApplication());
        $this->assertSame($console, $command->getConsole());
        $this->assertTrue($command->hasApplication());
        $this->assertTrue($command->hasConsole());
    }

    public function testSetAndGetApplication()
    {
        $command = new Command('hello');
        $this->assertFalse($command->hasApplication());
        $command->setApplication(new Application());
        $this->assertTrue($command->hasApplication());
        $this->assertInstanceOf('Pop\Application', $command->getApplication());
        $this->assertInstanceOf('Pop\Application', $command->application());
    }

    public function testSetAndGetConsole()
    {
        $command = new Command('hello');
        $this->assertFalse($command->hasConsole());
        $command->setConsole(new Console(80));
        $this->assertTrue($command->hasConsole());
        $this->assertInstanceOf('Pop\Console\Console', $command->getConsole());
        $this->assertInstanceOf('Pop\Console\Console', $command->console());
    }

    public function testDispatchWithoutHandleMethodThrowsException()
    {
        $this->expectException('Pop\Dispatch\Exception');

        $command = new Command('hello');
        $command->dispatch();
    }

    public function testLoad()
    {
        $application = new Application();
        $console     = new Console(80);

        $command = Command::load('hello', [
            'params'      => '-v',
            'help'        => 'Hello World',
            'application' => $application,
            'console'     => $console
        ]);

        $this->assertInstanceOf('Pop\Console\Command', $command);
        $this->assertEquals('hello', $command->getName());
        $this->assertEquals('-v', $command->getParams());
        $this->assertEquals('Hello World', $command->getHelp());
        $this->assertSame($application, $command->getApplication());
        $this->assertSame($console, $command->getConsole());
    }

    public function testLoadWithEmptyConfig()
    {
        $command = Command::load('hello', []);

        $this->assertInstanceOf('Pop\Console\Command', $command);
        $this->assertEquals('hello', $command->getName());
        $this->assertFalse($command->hasParams());
        $this->assertFalse($command->hasHelp());
        $this->assertFalse($command->hasApplication());
        $this->assertFalse($command->hasConsole());
    }

    public function testLoadForApplication()
    {
        $application = new Application();
        $console     = new Console(80);

        $command = Command::loadForApplication($application, $console, 'hello', [
            'params' => '-v',
            'help'   => 'Hello World'
        ]);

        $this->assertInstanceOf('Pop\Console\Command', $command);
        $this->assertEquals('hello', $command->getName());
        $this->assertEquals('-v', $command->getParams());
        $this->assertEquals('Hello World', $command->getHelp());
        $this->assertSame($application, $command->getApplication());
        $this->assertSame($console, $command->getConsole());
    }

}
