<?php

namespace Pop\Console\Test;

use Pop\Console\Console;
use Pop\Console\Command;

class ConsoleTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $console = new Console(100, '    ');
        $this->assertInstanceOf('Pop\Console\Console', $console);
        $this->assertEquals(100, $console->getWidth());
        $this->assertEquals('    ', $console->getIndent());

    }

    public function testAddAndGetCommand()
    {
        $console = new Console();
        $console->addCommand(new Command('help'));
        $this->assertEquals('help', $console->getCommand('help')->getName());
    }

    public function testAddAndGetCommands()
    {
        $console = new Console();
        $console->addCommand(new Command('help'));
        $console->addCommands([
            new Command('list'),
            new Command('print')
        ]);
        $this->assertTrue($console->hasCommand('help'));
        $this->assertEquals('help', $console->getCommand('help')->getName());
        $this->assertEquals('list', $console->getCommand('list')->getName());
        $this->assertEquals('print', $console->getCommand('print')->getName());
        $this->assertEquals(3, count($console->getCommands()));
    }

    public function testGetCommands()
    {
        $console = new Console();
        $console->addCommand(new Command('help'));
        $console->addCommands([
            new Command('list'),
            new Command('print')
        ]);
        $this->assertEquals(3, count($console->getCommands()));
    }

    public function testGetHelp()
    {
        $command = new Command('hello');
        $command->setHelp('Hello World');
        $console = new Console();
        $console->addCommand($command);
        $this->assertEquals('Hello World', $console->help('hello'));
    }

    public function testColor()
    {
        $console = new Console();
        $string = $console->colorize('Hello World', Console::BOLD_BLUE, Console::RED);
        $this->assertContains('[1;34m', $string);
        $this->assertContains('[41m', $string);
        $this->assertContains('[0m', $string);
    }

    public function testBadColor()
    {
        $console = new Console();
        $string = $console->colorize('Hello World', 'BAD_FG', 'BAD_BG');
        $this->assertContains('Hello World', $string);
    }

    public function testAppend()
    {
        $console = new Console();
        $console->append('Hello World');

        ob_start();
        $console->send();
        $result = ob_get_clean();

        $this->assertEquals('Hello World' . PHP_EOL, $result);
    }

    public function testWrite()
    {
        $console = new Console();

        ob_start();
        $console->write('Hello World');
        $result = ob_get_clean();

        $this->assertEquals('Hello World' . PHP_EOL, $result);
    }

    public function testWriteZero()
    {
        $console = new Console(0);

        ob_start();
        $console->write('Hello World');
        $result = ob_get_clean();

        $this->assertEquals('Hello World' . PHP_EOL, $result);
    }

    public function testClear()
    {
        $console = new Console();
        ob_start();
        $console->clear();
        $result = ob_get_clean();
        $this->assertNotNull($result);
    }

}