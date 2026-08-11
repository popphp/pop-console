<?php

namespace Pop\Console\Test\Command;

use Pop\Application;
use Pop\Console\Command\AbstractCommand;
use Pop\Console\Console;
use PHPUnit\Framework\TestCase;

class AbstractCommandTest extends TestCase
{

    public function testImplementsCommandInterface()
    {
        $command = new class extends AbstractCommand {};
        $this->assertInstanceOf('Pop\Console\Command\CommandInterface', $command);
    }

    public function testImplementsDispatchableInterface()
    {
        $command = new class extends AbstractCommand {};
        $this->assertInstanceOf('Pop\Dispatch\DispatchableInterface', $command);
    }

    public function testConstructorSetsAllProperties()
    {
        $application = new Application();
        $console     = new Console(80);
        $command     = new class('hello', '-v', 'Hello World', $application, $console) extends AbstractCommand {};

        $this->assertEquals('hello', $command->getName());
        $this->assertEquals('-v', $command->getParams());
        $this->assertEquals('Hello World', $command->getHelp());
        $this->assertSame($application, $command->getApplication());
        $this->assertSame($console, $command->getConsole());
    }

    public function testFluentSettersReturnSameConcreteInstance()
    {
        $command = new class extends AbstractCommand {};

        $result = $command->setName('hello')
            ->setParams('-v')
            ->setHelp('Hello World')
            ->setApplication(new Application())
            ->setConsole(new Console(80));

        $this->assertSame($command, $result);
        $this->assertInstanceOf(get_class($command), $result);
    }

    public function testDispatchCallsHandleWithNoActionGiven()
    {
        $command = new class extends AbstractCommand {
            public bool $called = false;
            public function handle(): void
            {
                $this->called = true;
            }
        };

        $command->dispatch();
        $this->assertTrue($command->called);
    }

    public function testDispatchPassesParamsToHandleWithNoActionGiven()
    {
        $command = new class extends AbstractCommand {
            public ?string $id = null;
            public function handle(string $id): void
            {
                $this->id = $id;
            }
        };

        $command->dispatch(null, ['123']);
        $this->assertEquals('123', $command->id);
    }

    public function testDispatchHonorsExplicitAction()
    {
        $command = new class extends AbstractCommand {
            public bool $called = false;
            public function handle(): void
            {
                $this->called = true;
            }
            public function other(): void
            {
                $this->called = false;
            }
        };

        $command->dispatch('other');
        $this->assertFalse($command->called);
    }

    public function testDispatchWithoutHandleMethodThrowsException()
    {
        $this->expectException('Pop\Dispatch\Exception');

        $command = new class extends AbstractCommand {};
        $command->dispatch();
    }

}
