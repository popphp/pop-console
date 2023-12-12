<?php

namespace Pop\Console\Test;

use Pop\Application;
use Pop\Console\Console;
use Pop\Console\Command;
use Pop\Console\Color;
use PHPUnit\Framework\TestCase;

class ConsoleTest extends TestCase
{

    public function testConstructor()
    {
        $console = new Console(100, '    ');
        $this->assertInstanceOf('Pop\Console\Console', $console);
        $this->assertEquals(100, $console->getWrap());
        $this->assertEquals('    ', $console->getIndent());
        $this->assertEquals(4, $console->getMargin());
        $this->assertTrue($console->hasMargin());
        $this->assertTrue($console->hasWidth());
        $this->assertTrue($console->hasHeight());
        $this->assertTrue($console->hasWrap());
        $this->assertGreaterThan(0, $console->getWidth());
        $this->assertGreaterThan(0, $console->getHeight());
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

    public function testGetAvailableColors()
    {
        $console = new Console();
        $colors = $console->getAvailableColors();
        $this->assertEquals(33, count($colors));
        $this->assertTrue(isset($colors['MAGENTA']));
        $this->assertEquals(6, $colors['MAGENTA']);
        $this->assertTrue(isset($colors['BOLD_WHITE']));
        $this->assertEquals(24, $colors['BOLD_WHITE']);
    }

    public function testSetAndGetHelpColors()
    {
        $console = new Console();
        $console->setHelpColors(Color::RED, Color::WHITE, Color::BLUE, Color::MAGENTA);
        $this->assertEquals(4, count($console->getHelpColors()));
    }

    public function testGetServer()
    {
        $console = new Console();
        $this->assertTrue(is_array($console->getServer()));
        $this->assertNull($console->getServer('foo'));
    }

    public function testGetEnv()
    {
        $console = new Console();
        $this->assertTrue(is_array($console->getEnv()));
        $this->assertNull($console->getEnv('foo'));
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
        $console = new Console(80, '    ');
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
        $console->setHelpColors(Color::BOLD_BLUE, Color::YELLOW, Color::BOLD_MAGENTA);

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

        $this->assertStringContainsString('user', $result);
    }

    public function testLine1()
    {
        $console = new Console(10);

        ob_start();
        $console->line();
        $result = ob_get_clean();

        $this->assertEquals('    ----------' . PHP_EOL, $result);
    }

    public function testLine2()
    {
        $console = new Console(null, 0);

        ob_start();
        $console->line();
        $result = ob_get_clean();

        $this->assertEquals(str_repeat('-', $console->getWidth()) . PHP_EOL, $result);
    }

    public function testHeader1()
    {
        $console = new Console();

        ob_start();
        $console->header('Hello World');
        $result = ob_get_clean();

        $this->assertEquals('    Hello World' . PHP_EOL . '    -----------' . PHP_EOL, $result);
    }

    public function testHeader2()
    {
        $console = new Console(20);

        ob_start();
        $console->header('Hello World', '-', 'auto');
        $result = ob_get_clean();

        $this->assertEquals('    Hello World' . PHP_EOL . '    --------------------' . PHP_EOL, $result);
    }

    public function testHeader3()
    {
        $console = new Console(20);

        ob_start();
        $console->header('Hello World', '-', 'auto', 'right');
        $result = ob_get_clean();

        $this->assertEquals('             Hello World' . PHP_EOL . '    --------------------' . PHP_EOL, $result);
    }

    public function testHeader4()
    {
        $console = new Console(null, 0);

        ob_start();
        $console->header('Hello World', '-', 'auto');
        $result = ob_get_clean();

        $this->assertEquals('Hello World' . PHP_EOL . str_repeat('-', $console->getWidth()) . PHP_EOL, $result);
    }

    public function testHeader5()
    {
        $console = new Console(10);

        ob_start();
        $console->header('Hello World', '-', null, 'right');
        $result = ob_get_clean();

        $this->assertEquals('         Hello' . PHP_EOL . '         World' . PHP_EOL . '    ----------' . PHP_EOL, $result);
    }

    public function testHeader6()
    {
        $console = new Console(null, 0);

        ob_start();
        $console->header('Hello World. This is a long string of text. This is a long string of text. This is a long string of text. This is a long string of text. This is a long string of text. This is a long string of text.', '-', null, 'center');
        $result = ob_get_clean();

        $this->assertStringContainsString('This is a long string of text', $result);
    }

    public function testHeaderLeft()
    {
        $console = new Console(20);

        ob_start();
        $console->headerLeft('Hello World');
        $result = ob_get_clean();

        $this->assertEquals('    Hello World' . PHP_EOL . '    --------------------' . PHP_EOL, $result);
    }

    public function testHeaderRight()
    {
        $console = new Console(20);

        ob_start();
        $console->headerRight('Hello World');
        $result = ob_get_clean();

        $this->assertEquals('             Hello World' . PHP_EOL . '    --------------------' . PHP_EOL, $result);
    }

    public function testHeaderCenter()
    {
        $console = new Console(20);

        ob_start();
        $console->headerCenter('Hello World');
        $result = ob_get_clean();

        $this->assertEquals('         Hello World' . PHP_EOL . '    --------------------' . PHP_EOL, $result);
    }

    public function testAlert1()
    {
        $console = new Console(20);

        ob_start();
        $console->alertDanger('Hello World. This is a longer alert.');
        $result = ob_get_clean();

        $this->assertTrue(str_contains($result, "\x1b[1;97m\x1b[101m    Hello World.    \x1b[0m"));
    }

    public function testAlert2()
    {
        $console = new Console(null);

        ob_start();
        $console->alertDanger('Hello World. This is a longer alert. This is a longer alert. This is a longer alert. This is a longer alert. This is a longer alert. This is a longer alert. This is a longer alert. This is a longer alert. This is a longer alert. This is a longer alert.');
        $result = ob_get_clean();

        $this->assertTrue(str_contains($result, "\x1b[1;97m\x1b[101m"));
        $this->assertTrue(str_contains($result, "Hello World. This is a longer alert."));
    }

    public function testAlert3()
    {
        $console = new Console(20);

        ob_start();
        $console->alertDanger('Hello World.', 'auto');
        $result = ob_get_clean();

        $this->assertTrue(str_contains($result, "\x1b[1;97m\x1b[101m    Hello World.    \x1b[0m"));
    }

    public function testAlert4()
    {
        $console = new Console(null);

        ob_start();
        $console->alertDanger('Hello World.', 'auto');
        $result = ob_get_clean();

        $this->assertTrue(str_contains($result, "\x1b[1;97m\x1b[101m"));
        $this->assertTrue(str_contains($result, "Hello World."));
    }

    public function testAlert5()
    {
        $console = new Console(null);

        ob_start();
        $console->alertDanger('Hello World.', 'auto', 'left');
        $result = ob_get_clean();

        $this->assertTrue(str_contains($result, "\x1b[1;97m\x1b[101m"));
        $this->assertTrue(str_contains($result, "Hello World."));
    }

    public function testAlert6()
    {
        $console = new Console(null);

        ob_start();
        $console->alertDanger('Hello World.', 'auto', 'right');
        $result = ob_get_clean();

        $this->assertTrue(str_contains($result, "\x1b[1;97m\x1b[101m"));
        $this->assertTrue(str_contains($result, "Hello World."));
    }

    public function testAlertDanger()
    {
        $console = new Console(20);

        ob_start();
        $console->alertDanger('Hello World');
        $result = ob_get_clean();

        $this->assertTrue(str_contains($result, "\x1b[1;97m\x1b[101m    Hello World    \x1b[0m"));
    }

    public function testAlertWarning()
    {
        $console = new Console(20);

        ob_start();
        $console->alertWarning('Hello World');
        $result = ob_get_clean();

        $this->assertTrue(str_contains($result, "\x1b[1;30m\x1b[103m    Hello World    \x1b[0m"));
    }

    public function testAlertSuccess()
    {
        $console = new Console(20);

        ob_start();
        $console->alertSuccess('Hello World');
        $result = ob_get_clean();

        $this->assertTrue(str_contains($result, "\x1b[1;30m\x1b[42m    Hello World    \x1b[0m"));
    }

    public function testAlertInfo()
    {
        $console = new Console(20);

        ob_start();
        $console->alertInfo('Hello World');
        $result = ob_get_clean();

        $this->assertTrue(str_contains($result, "\x1b[1;30m\x1b[104m    Hello World    \x1b[0m"));
    }

    public function testAlertPrimary()
    {
        $console = new Console(20);

        ob_start();
        $console->alertPrimary('Hello World');
        $result = ob_get_clean();

        $this->assertTrue(str_contains($result, "\x1b[1;97m\x1b[44m    Hello World    \x1b[0m"));
    }

    public function testAlertSecondary()
    {
        $console = new Console(20);

        ob_start();
        $console->alertSecondary('Hello World');
        $result = ob_get_clean();

        $this->assertTrue(str_contains($result, "\x1b[1;97m\x1b[45m    Hello World    \x1b[0m"));
    }

    public function testAlertDark()
    {
        $console = new Console(20);

        ob_start();
        $console->alertDark('Hello World');
        $result = ob_get_clean();

        $this->assertTrue(str_contains($result, "\x1b[1;97m\x1b[100m    Hello World    \x1b[0m"));
    }

    public function testAlertLight()
    {
        $console = new Console(20);

        ob_start();
        $console->alertLight('Hello World');
        $result = ob_get_clean();

        $this->assertTrue(str_contains($result, "\x1b[1;30m\x1b[47m    Hello World    \x1b[0m"));
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

    public function testPrompt()
    {
        $_SERVER['X_POP_CONSOLE_INPUT'] = 'y';

        ob_start();
        $console = new Console();
        $answer  = $console->prompt('Test prompt: ');
        $result = ob_get_clean();

        $this->assertEquals('y', $answer);
    }

    public function testPromptWithIndent()
    {
        $_SERVER['X_POP_CONSOLE_INPUT'] = 'y';

        ob_start();
        $console = new Console();
        $console->setHeader('Test Header:');
        $console->setIndent('    ');
        $answer  = $console->prompt('Test prompt: ');
        $result = ob_get_clean();

        $this->assertTrue(str_contains($result, '    Test prompt: '));
    }

    public function testPromptWithHeader()
    {
        $_SERVER['X_POP_CONSOLE_INPUT'] = 'y';

        ob_start();
        $console = new Console();
        $console->setHeader('Test Header:');
        $answer  = $console->prompt('Test prompt: ');
        $result = ob_get_clean();

        $this->assertTrue(str_contains($result, 'Test Header:'));
    }

    public function testPromptWithOptions()
    {
        $_SERVER['X_POP_CONSOLE_INPUT'] = 'n';

        ob_start();
        $console = new Console();
        $console->setIndent('    ');
        $answer  = $console->prompt('Test prompt: ', ['Y', 'N']);
        $result = ob_get_clean();

        $this->assertEquals('n', $answer);
        $this->assertTrue(str_contains($result, '    Test prompt: '));
    }

    public function testConfirmYes()
    {
        $_SERVER['X_POP_CONSOLE_INPUT'] = 'y';

        ob_start();
        $console = new Console();
        $answer  = $console->confirm();
        $result = ob_get_clean();

        $this->assertEquals('y', $answer);
    }

}
