<?php

namespace Pop\Console\Test;

use Pop\Application;
use Pop\Console\Console;
use Pop\Console\Command;
use PHPUnit\Framework\TestCase;

class ConsoleTest extends TestCase
{

    public function testConstructor()
    {
        $console = new Console(100, '    ');
        $this->assertInstanceOf('Pop\Console\Console', $console);
        $this->assertEquals(100, $console->getWidth());
        $this->assertEquals('    ', $console->getIndent());
    }

    public function testSetAndGetHeader()
    {
        $console = new Console();
        $console->setHeader('header');
        $this->assertEquals('header', $console->getHeader());
    }

    public function testSetAndGetFooter()
    {
        $console = new Console();
        $console->setFooter('footer');
        $this->assertEquals('footer', $console->getFooter());
    }

    public function testSetAndGetHeaderSent()
    {
        $console = new Console();
        $console->setHeaderSent(true);
        $this->assertTrue($console->getHeaderSent());
    }

    public function testSetAndGetHelpColors()
    {
        $console = new Console();
        $console->setHelpColors(Console::RED, Console::WHITE, Console::BLUE);
        $this->assertEquals(3, count($console->getHelpColors()));
    }

    public function testAddAndGetCommand()
    {
        $console = new Console();
        $console->addCommand(new Command('help'));
        $this->assertEquals('help', $console->getCommand('help')->getName());
    }

    public function testAddCommandsFromRoutes()
    {
        $app     = new Application(['routes' => [
            'app:init [--web] [--api] [--cli] <namespace>' => [
                'controller' => 'MyAppController',
                'action'     => 'init',
                'help'       => 'Init application' . PHP_EOL
            ],
            'db:config' => [
                'controller' => 'MyAppController',
                'action'     => 'config',
                'help'       => 'Config DB'
            ]
        ]]);

        $console = new Console();
        $console->addCommandsFromRoutes($app->router()->getRouteMatch(), './app');
        $this->assertEquals('./app app:init', $console->getCommand('./app app:init')->getName());
        $this->assertEquals('./app db:config', $console->getCommand('./app db:config')->getName());
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

    public function testDisplayHelp()
    {
        $command = new Command('hello', '-v', 'This is the help');
        $console = new Console();
        $console->addCommand($command);

        ob_start();
        $console->help();
        $result = ob_get_clean();

        $this->assertEquals('    hello -v    This is the help' . PHP_EOL, $result);
    }

    public function testDisplayHelpColors()
    {
        $userList   = new Command('user list', '-v --option=123 [<id>]', 'This is the users list command.');
        $userAdd    = new Command('user', '--name=');
        $userEdit   = new Command('user edit', '<id>', 'This is the users edit command.');
        $userDelete = new Command('user delete', '<id>', 'This is the users delete command. This is the users delete command. This is the users delete command. This is the users delete command. This is the users delete command.');
        $userShow   = new Command('user show', '-v --option=123 [<id>]', 'This is the users list command.');

        $console = new Console(80, '    ');
        $console->setHelpColors(Console::BOLD_BLUE, Console::YELLOW, Console::BOLD_MAGENTA);

        $console->setHeader(
            <<<HEADER

Pop Console Example App
=======================

HEADER
        );


        $console->setFooter('-----------------');

        $console->addCommands([
            $userList,
            $userAdd,
            $userEdit,
            $userDelete,
            $userShow
        ]);

        ob_start();
        $console->help();
        $result = ob_get_clean();

        $this->assertContains('user', $result);
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

        $this->assertEquals('    Hello World' . PHP_EOL, $result);
    }

    public function testWrite()
    {
        $console = new Console();

        ob_start();
        $console->write('Hello World');
        $result = ob_get_clean();

        $this->assertEquals('    Hello World' . PHP_EOL, $result);
    }

    public function testWriteZero()
    {
        $console = new Console(0);

        ob_start();
        $console->write('Hello World');
        $result = ob_get_clean();

        $this->assertEquals('    Hello World' . PHP_EOL, $result);
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