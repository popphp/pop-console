<?php

namespace Pop\Console\Test;

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

    public function testAddAndGetCommand()
    {
        $console = new Console();
        $console->addCommand(new Input\Command('help'));
        $this->assertEquals('help', $console->getCommand('help')->getName());
    }

    public function testAddAndGetCommands()
    {
        $console = new Console();
        $console->addCommand(new Input\Command('help'));
        $console->addCommands([
            new Input\Command('list'),
            new Input\Command('print')
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
        $console->addCommand(new Input\Command('help'));
        $console->addCommands([
            new Input\Command('list'),
            new Input\Command('print')
        ]);
        $this->assertEquals(3, count($console->getCommands()));
    }

    public function testAddAndGetOption()
    {
        $console = new Console();
        $console->addOption(new Input\Option('-h'));
        $this->assertEquals('-h', $console->getOption('-h')->getShortName());
    }

    public function testAddAndGetOptions()
    {
        $console = new Console();
        $console->addOption(new Input\Option('-h'));
        $console->addOptions([
            new Input\Option('--list'),
            new Input\Option('--print')
        ]);
        $this->assertTrue($console->hasOption('-h'));
        $this->assertEquals('-h', $console->getOption('-h')->getShortName());
        $this->assertEquals('--list', $console->getOption('--list')->getLongName());
        $this->assertEquals('--print', $console->getOption('--print')->getLongName());
        $this->assertEquals(3, count($console->getOptions()));
    }

    public function testGetOptions()
    {
        $console = new Console();
        $console->addOption(new Input\Option('-h'));
        $console->addOptions([
            new Input\Option('--list'),
            new Input\Option('--print')
        ]);
        $this->assertEquals(3, count($console->getOptions()));
    }

    public function testGetArguments()
    {
        $_SERVER['argv'][] = '-h';
        $console = new Console();
        $this->assertTrue($console->hasArgument('-h'));
        $this->assertContains('-h', $console->getArguments());
    }

    public function testGetOptionValue()
    {
        $_SERVER['argv'][] = '--name=Test';
        $console = new Console();
        $console->addOption(new Input\Option('--name', Input\Option::VALUE_REQUIRED));
        $this->assertEquals('Test', $console->get('--name'));
        $this->assertEquals('Test', $console['--name']);
    }

    public function testGetCommandValue()
    {
        $_SERVER['argv'][] = 'print';
        $_SERVER['argv'][] = 'info';
        $console = new Console();
        $console->addCommand(new Input\Command('print', Input\Command::VALUE_REQUIRED));
        $this->assertEquals('info', $console->get('print'));
        $this->assertEquals('info', $console['print']);
        $this->assertEquals('info', $console->print);
        $this->assertTrue(isset($console['print']));
        $this->assertTrue(isset($console->print));
        $this->setExpectedException('Pop\Console\Exception');
        $console['print'] = 'bad';
    }

    public function testGetRequiredParamsNotFound()
    {
        $_SERVER['argv'][] = 'print';
        $console = new Console();
        $console->addCommand(new Input\Command('print', Input\Command::VALUE_REQUIRED));
        $this->assertEquals(1, count($console->getRequiredParamsNotFound()));
    }

    public function testIsRequestValid()
    {
        $_SERVER['argv'][] = 'print';
        $console = new Console();
        $console->addCommand(new Input\Command('print', Input\Command::VALUE_REQUIRED));
        $this->assertFalse($console->isRequestValid());
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

    public function testWrite()
    {
        $console = new Console();
        $console->write('Hello World');

        ob_start();
        $console->send();
        $result = ob_get_clean();

        $this->assertEquals('Hello World' . PHP_EOL, $result);
    }

    public function testWriteZero()
    {
        $console = new Console(0);
        $console->write('Hello World');

        ob_start();
        $console->send();
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

    public function testOffsetSetException()
    {
        $_SERVER['argv'][] = 'print';
        $_SERVER['argv'][] = 'info';
        $console = new Console();
        $console->addCommand(new Input\Command('print', Input\Command::VALUE_REQUIRED));
        $this->setExpectedException('Pop\Console\Exception');
        $console['print'] = 'bad';
    }

    public function testOffsetUnsetException()
    {
        $_SERVER['argv'][] = 'print';
        $_SERVER['argv'][] = 'info';
        $console = new Console();
        $console->addCommand(new Input\Command('print', Input\Command::VALUE_REQUIRED));
        $this->setExpectedException('Pop\Console\Exception');
        unset($console['print']);
    }

}