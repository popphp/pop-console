<?php

namespace Pop\COnsole\Test;

use Pop\Console\Console;
use Pop\Console\Input;

class ConsoleTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $console = new Console();
        $this->assertInstanceOf('Pop\Console\Console', $console);
        $this->assertInstanceOf('Pop\Console\Request', $console->request());
        $this->assertInstanceOf('Pop\Console\Response', $console->response());
        $this->assertEquals(80, $console->getWidth());
    }

    public function testAddAndGetCommands()
    {
        $console = new Console();
        $console->addCommand(new Input\Command('help'));
        $console->addCommands([
            new Input\Command('list'),
            new Input\Command('print')
        ]);
        $this->assertEquals('help', $console->getCommand('help')->getName());
        $this->assertEquals('list', $console->getCommand('list')->getName());
        $this->assertEquals('print', $console->getCommand('print')->getName());
        $this->assertEquals(3, count($console->getCommands()));
    }

    public function testGetCommands()
    {
        $console = new Console();
        $console->addCommand(new Input\Command('help'));
        $console->addCommands([
            new Input\Command('list'),
            new Input\Command('print')
        ]);
        $this->assertEquals(3, count($console->getCommands()));
    }

}