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
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }
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
        $this->assertIsBool($console->isColor());
        $this->assertIsBool($console->isWindows());
    }

    public function testIsWindows()
    {
        $console  = new Console();
        $expected = (stripos(PHP_OS, 'win') !== false);
        $this->assertEquals($expected, $console->isWindows());
    }

    public function testSetAndGetHeader()
    {
        $console = new Console();
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }
        $console->setHeader('header');
        $this->assertEquals('header' . PHP_EOL, $console->getHeader());
    }

    public function testSetAndGetFooter()
    {
        $console = new Console();
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }
        $console->setFooter('footer');
        $this->assertEquals(PHP_EOL . 'footer', $console->getFooter());
    }

    public function testSetAndGetHeaderSent()
    {
        $console = new Console();
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }
        $console->setHeaderSent(true);
        $this->assertTrue($console->getHeaderSent());
    }

    public function testGetAvailableColors()
    {
        $console = new Console();
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }
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
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }
        $console->setHelpColors(Color::RED, Color::WHITE, Color::BLUE, Color::MAGENTA);
        $this->assertEquals(4, count($console->getHelpColors()));
    }

    public function testGetServer()
    {
        $console = new Console();
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }
        $this->assertTrue(is_array($console->getServer()));
        $this->assertNull($console->getServer('foo'));
    }

    public function testGetEnv()
    {
        $console = new Console();
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }
        $this->assertTrue(is_array($console->getEnv()));
        $this->assertNull($console->getEnv('foo'));
    }

    public function testAddAndGetCommand()
    {
        $console = new Console();
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }
        $console->addCommand(new Command(name: 'help'));
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
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }
        $console->addCommandsFromRoutes($app->router()->getRouteMatch(), './app');
        $this->assertEquals('./app app:init', $console->getCommand('./app app:init')->getName());
        $this->assertEquals('./app db:config', $console->getCommand('./app db:config')->getName());
    }

    public function testAddAndGetCommands()
    {
        $console = new Console();
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }
        $console->addCommand(new Command(name: 'help'));
        $console->addCommands([
            new Command(name: 'list'),
            new Command(name: 'print')
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
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }
        $console->addCommand(new Command(name: 'help'));
        $console->addCommands([
            new Command(name: 'list'),
            new Command(name: 'print')
        ]);
        $this->assertEquals(3, count($console->getCommands()));
    }

    public function testGetHelp()
    {
        $command = new Command(name: 'hello');
        $command->setHelp('Hello World');
        $console = new Console();
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }
        $console->addCommand($command);
        $this->assertEquals('Hello World', $console->help('hello'));
    }

    public function testDisplayHelp()
    {
        $command = new Command(name: 'hello', params: '-v', help: 'This is the help');
        $console = new Console(80, '    ');
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }
        $console->addCommand($command);

        ob_start();
        $console->help();
        $result = ob_get_clean();

        $this->assertEquals('    hello -v    This is the help' . PHP_EOL, $result);
    }

    public function testDisplayHelpAddsBlankLineAfterWrappedNonLastCommand()
    {
        $wrapped = new Command(
            name: 'wrapped',
            help: 'This help text is long enough that it will wrap across multiple lines given a narrow wrap width.'
        );
        $short = new Command(name: 'short', help: 'Short help.');

        $console = new Console(40, '    ');
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }
        $console->addCommands([$wrapped, $short]);

        ob_start();
        $console->help();
        $result = ob_get_clean();

        $wrappedPos = strpos($result, 'wrapped');
        $shortPos   = strpos($result, 'short');

        $this->assertNotFalse($wrappedPos);
        $this->assertNotFalse($shortPos);
        $this->assertStringContainsString(
            PHP_EOL . PHP_EOL, substr($result, $wrappedPos, $shortPos - $wrappedPos)
        );
    }

    public function testDisplayHelpColors()
    {
        $userList   = new Command(name: 'user list', params: '-v --option=123 [<id>]', help: 'This is the users list command.');
        $userAdd    = new Command(name: 'user', params: '--name=');
        $userEdit   = new Command(name: 'user edit', params: '<id>', help: 'This is the users edit command.');
        $userDelete = new Command(name: 'user delete', params: '<id>', help: 'This is the users delete command. This is the users delete command. This is the users delete command. This is the users delete command. This is the users delete command.');
        $userShow   = new Command(name: 'user show', params: '-v --option=123 [<id>]', help: 'This is the users list command.');

        $console = new Console(80, '    ');
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }
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
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }

        ob_start();
        $console->line();
        $result = ob_get_clean();

        $this->assertEquals('    ----------' . PHP_EOL, $result);
    }

    public function testLine2()
    {
        $console = new Console(null, 0);
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }

        $result = $console->line('-', null, true, true);

        $this->assertEquals(str_repeat('-', $console->getWidth()) . PHP_EOL, $result);
    }

    public function testHeader1()
    {
        $console = new Console();
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }

        ob_start();
        $console->header('Hello World');
        $result = ob_get_clean();

        $this->assertEquals('    Hello World' . PHP_EOL . '    -----------' . PHP_EOL, $result);
    }

    public function testHeader2()
    {
        $console = new Console(20);
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }

        $result = $console->header('Hello World', '-', 'auto', 'left', true, true);

        $this->assertEquals('    Hello World' . PHP_EOL . '    --------------------' . PHP_EOL, $result);
    }

    public function testHeader3()
    {
        $console = new Console(20);
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }

        ob_start();
        $console->header('Hello World', '-', 'auto', 'right');
        $result = ob_get_clean();

        $this->assertEquals('             Hello World' . PHP_EOL . '    --------------------' . PHP_EOL, $result);
    }

    public function testHeader4()
    {
        $console = new Console(null, 0);
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }

        ob_start();
        $console->header('Hello World', '-', 'auto');
        $result = ob_get_clean();

        $this->assertEquals('Hello World' . PHP_EOL . str_repeat('-', $console->getWidth()) . PHP_EOL, $result);
    }

    public function testHeader5()
    {
        $console = new Console(10);
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }

        ob_start();
        $console->header('Hello World', '-', null, 'right');
        $result = ob_get_clean();

        $this->assertEquals('         Hello' . PHP_EOL . '         World' . PHP_EOL . '    ----------' . PHP_EOL, $result);
    }

    public function testHeader6()
    {
        $console = new Console(null, 0);
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }

        ob_start();
        $console->header('Hello World. This is a long string of text. This is a long string of text. This is a long string of text. This is a long string of text. This is a long string of text. This is a long string of text.', '-', null, 'center');
        $result = ob_get_clean();

        $this->assertStringContainsString('This is a long string of text', $result);
    }

    public function testHeaderLeft()
    {
        $console = new Console(20);
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }

        ob_start();
        $console->headerLeft('Hello World');
        $result = ob_get_clean();

        $this->assertEquals('    Hello World' . PHP_EOL . '    --------------------' . PHP_EOL, $result);
    }

    public function testHeaderRight()
    {
        $console = new Console(20);
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }

        ob_start();
        $console->headerRight('Hello World');
        $result = ob_get_clean();

        $this->assertEquals('             Hello World' . PHP_EOL . '    --------------------' . PHP_EOL, $result);
    }

    public function testHeaderCenter()
    {
        $console = new Console(20);
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }

        ob_start();
        $console->headerCenter('Hello World');
        $result = ob_get_clean();

        $this->assertEquals('         Hello World' . PHP_EOL . '    --------------------' . PHP_EOL, $result);
    }

    public function testAlert1()
    {
        $console = new Console(20);
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }

        ob_start();
        $console->alertDanger('Hello World. This is a longer alert.');
        $result = ob_get_clean();

        $this->assertTrue(str_contains($result, "\x1b[1;97m\x1b[101m    Hello World.    \x1b[0m"));
    }

    public function testAlert2()
    {
        $console = new Console(null);
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }

        $result = $console->alertDanger(
            'Hello World. This is a longer alert. This is a longer alert. This is a longer alert. This is a longer alert. This is a longer alert. This is a longer alert. This is a longer alert. This is a longer alert. This is a longer alert. This is a longer alert.',
            null, 'center', 4, true, true);

        $this->assertTrue(str_contains($result, "\x1b[1;97m\x1b[101m"));
        $this->assertTrue(str_contains($result, "Hello World. This is a longer alert."));
    }

    public function testAlert3()
    {
        $console = new Console(20);
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }

        ob_start();
        $console->alertDanger('Hello World.', 'auto');
        $result = ob_get_clean();

        $this->assertTrue(str_contains($result, "\x1b[1;97m\x1b[101m    Hello World.    \x1b[0m"));
    }

    public function testAlert4()
    {
        $console = new Console(null);
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }

        ob_start();
        $console->alertDanger('Hello World.', 'auto');
        $result = ob_get_clean();

        $this->assertTrue(str_contains($result, "\x1b[1;97m\x1b[101m"));
        $this->assertTrue(str_contains($result, "Hello World."));
    }

    public function testAlert5()
    {
        $console = new Console(null);
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }

        ob_start();
        $console->alertDanger('Hello World.', 'auto', 'left');
        $result = ob_get_clean();

        $this->assertTrue(str_contains($result, "\x1b[1;97m\x1b[101m"));
        $this->assertTrue(str_contains($result, "Hello World."));
    }

    public function testAlert6()
    {
        $console = new Console(null);
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }

        ob_start();
        $console->alertDanger('Hello World.', 'auto', 'right');
        $result = ob_get_clean();

        $this->assertTrue(str_contains($result, "\x1b[1;97m\x1b[101m"));
        $this->assertTrue(str_contains($result, "Hello World."));
    }

    public function testAlertDanger()
    {
        $console = new Console(20);
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }

        ob_start();
        $console->alertDanger('Hello World');
        $result = ob_get_clean();

        $this->assertTrue(str_contains($result, "\x1b[1;97m\x1b[101m    Hello World    \x1b[0m"));
    }

    public function testAlertWarning()
    {
        $console = new Console(20);
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }

        ob_start();
        $console->alertWarning('Hello World');
        $result = ob_get_clean();

        $this->assertTrue(str_contains($result, "\x1b[1;30m\x1b[103m    Hello World    \x1b[0m"));
    }

    public function testAlertSuccess()
    {
        $console = new Console(20);
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }

        ob_start();
        $console->alertSuccess('Hello World');
        $result = ob_get_clean();

        $this->assertTrue(str_contains($result, "\x1b[1;30m\x1b[42m    Hello World    \x1b[0m"));
    }

    public function testAlertInfo()
    {
        $console = new Console(20);
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }

        ob_start();
        $console->alertInfo('Hello World');
        $result = ob_get_clean();

        $this->assertTrue(str_contains($result, "\x1b[1;97m\x1b[104m    Hello World    \x1b[0m"));
    }

    public function testAlertPrimary()
    {
        $console = new Console(20);
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }

        ob_start();
        $console->alertPrimary('Hello World');
        $result = ob_get_clean();

        $this->assertTrue(str_contains($result, "\x1b[1;97m\x1b[44m    Hello World    \x1b[0m"));
    }

    public function testAlertSecondary()
    {
        $console = new Console(20);
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }

        ob_start();
        $console->alertSecondary('Hello World');
        $result = ob_get_clean();

        $this->assertTrue(str_contains($result, "\x1b[1;97m\x1b[45m    Hello World    \x1b[0m"));
    }

    public function testAlertDark()
    {
        $console = new Console(20);
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }

        ob_start();
        $console->alertDark('Hello World');
        $result = ob_get_clean();

        $this->assertTrue(str_contains($result, "\x1b[1;97m\x1b[100m    Hello World    \x1b[0m"));
    }

    public function testAlertLight()
    {
        $console = new Console(20);
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }

        ob_start();
        $console->alertLight('Hello World');
        $result = ob_get_clean();

        $this->assertTrue(str_contains($result, "\x1b[1;30m\x1b[47m    Hello World    \x1b[0m"));
    }

    public function testAlertBox1()
    {
        $console = new Console(20);
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }

        ob_start();
        $console->alertBox('Hello World');
        $result = ob_get_clean();

        $this->assertTrue(str_contains($result, "   -------------------"));
        $this->assertTrue(str_contains($result, "    |   Hello World   |"));
    }

    public function testAlertBox2()
    {
        $console = new Console(20);
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }

        $result = $console->alertBox(
            'Hello World. This is a longer alert. This is a longer alert.',
            '-', '|', null, 'center', 4, true, true
        );

        $this->assertTrue(str_contains($result, "   -------------------"));
        $this->assertTrue(str_contains($result, "    |   Hello World"));
    }

    public function testAlertBox3()
    {
        $console = new Console(null);
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }

        ob_start();
        $console->alertBox('Hello World. This is a longer alert. This is a longer alert. This is a longer alert. This is a longer alert. This is a longer alert. This is a longer alert. This is a longer alert. This is a longer alert. This is a longer alert. This is a longer alert.');
        $result = ob_get_clean();

        $this->assertTrue(str_contains($result, "   -------------------"));
        $this->assertTrue(str_contains($result, "Hello World"));
    }

    public function testAlertBox4()
    {
        $console = new Console(80);
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }

        ob_start();
        $console->alertBox('Hello World. This is a longer alert.', '-', '|', 'auto', 'center');
        $result = ob_get_clean();

        $this->assertTrue(str_contains($result, "-------------------"));
        $this->assertTrue(str_contains($result, "Hello World"));
    }

    public function testAlertBox5()
    {
        $console = new Console(null);
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }

        ob_start();
        $console->alertBox('Hello World. This is a longer alert.', '-', '|', 'auto', 'right');
        $result = ob_get_clean();

        $this->assertTrue(str_contains($result, "-------------------"));
        $this->assertTrue(str_contains($result, "Hello World"));
    }

    public function testAlertBox6()
    {
        $console = new Console(null);
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }

        ob_start();
        $console->alertBox('Hello World. This is a longer alert.', '-', '|', 'auto', 'left');
        $result = ob_get_clean();

        $this->assertTrue(str_contains($result, "-------------------"));
        $this->assertTrue(str_contains($result, "Hello World"));
    }

    public function testColor()
    {
        $console = new Console();
        $string  = $console->colorize('Hello World', Color::BOLD_BLUE, Color::RED);
        $this->assertStringContainsString('[1;34m', $string);
        $this->assertStringContainsString('[41m', $string);
        $this->assertStringContainsString('[0m', $string);
    }

    public function testAppend()
    {
        $console = new Console();
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }
        $console->append('Hello World');

        ob_start();
        $console->send();
        $result = ob_get_clean();

        $this->assertEquals('    Hello World' . PHP_EOL, $result);
    }

    public function testWrite()
    {
        $console = new Console();
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }

        ob_start();
        $console->write('Hello World');
        $result = ob_get_clean();

        $this->assertEquals('    Hello World' . PHP_EOL, $result);
    }

    public function testWriteZero()
    {
        $console = new Console(0);
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }

        ob_start();
        $console->write('Hello World');
        $result = ob_get_clean();

        $this->assertEquals('    Hello World' . PHP_EOL, $result);
    }

    public function testClear()
    {
        $console = new Console();
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }
        ob_start();
        $console->clear();
        $result = ob_get_clean();
        $this->assertNotNull($result);
    }

    private function createInputStream(string ...$lines): mixed
    {
        $stream = fopen('php://memory', 'r+');
        foreach ($lines as $line) {
            fwrite($stream, $line . PHP_EOL);
        }
        rewind($stream);
        return $stream;
    }

    public function testSetAndGetInputStream()
    {
        $console = new Console();
        $stream  = $this->createInputStream('y');
        $console->setInputStream($stream);

        $this->assertTrue($console->hasInputStream());
        $this->assertSame($stream, $console->getInputStream());
    }

    public function testSetInputStreamWithNonResourceThrowsException()
    {
        $this->expectException('Pop\Console\Exception');

        $console = new Console();
        $console->setInputStream('not a resource');
    }

    public function testMultiLinePromptSequenceFromSingleStream()
    {
        $console = new Console();
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }
        $console->setInputStream($this->createInputStream('x', 'y'));

        ob_start();
        $first  = $console->prompt('Test prompt: ');
        $second = $console->prompt('Test prompt: ');
        ob_get_clean();

        $this->assertEquals('x', $first);
        $this->assertEquals('y', $second);
    }

    public function testPrompt()
    {
        $console = new Console();
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }
        $console->setInputStream($this->createInputStream('y'));

        ob_start();
        $answer  = $console->prompt('Test prompt: ');
        $result = ob_get_clean();

        $this->assertEquals('y', $answer);
    }

    public function testPromptWithIndent()
    {
        $console = new Console();
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }
        $console->setInputStream($this->createInputStream('y'));
        $console->setHeader('Test Header:');
        $console->setIndent('    ');

        ob_start();
        $answer  = $console->prompt('Test prompt: ');
        $result = ob_get_clean();

        $this->assertTrue(str_contains($result, '    Test prompt: '));
    }

    public function testPromptWithHeader()
    {
        $console = new Console();
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }
        $console->setInputStream($this->createInputStream('y'));
        $console->setHeader('Test Header:');

        ob_start();
        $answer  = $console->prompt('Test prompt: ');
        $result = ob_get_clean();

        $this->assertTrue(str_contains($result, 'Test Header:'));
    }

    public function testPromptWithOptions()
    {
        $console = new Console();
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }
        $console->setInputStream($this->createInputStream('n'));
        $console->setIndent('    ');

        ob_start();
        $answer  = $console->prompt('Test prompt: ', ['Y', 'N']);
        $result = ob_get_clean();

        $this->assertEquals('n', $answer);
        $this->assertTrue(str_contains($result, '    Test prompt: '));
    }

    public function testConfirmYes()
    {
        $console = new Console();
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }
        $console->setInputStream($this->createInputStream('y'));

        ob_start();
        $answer  = $console->confirm();
        $result = ob_get_clean();

        $this->assertEquals('y', $answer);
    }

    public function testConfirmNoWithExitFalseReturnsResponseWithoutExiting()
    {
        $console = new Console();
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }
        $console->setInputStream($this->createInputStream('n'));

        ob_start();
        $answer = $console->confirm(exit: false);
        ob_get_clean();

        $this->assertEquals('n', $answer);
    }

    public function testPromptRetriesOnInvalidOption()
    {
        $console = new Console();
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }
        $console->setInputStream($this->createInputStream('x', 'y'));

        ob_start();
        $answer = $console->prompt('Test prompt: ', ['Y', 'N']);
        $result = ob_get_clean();

        $this->assertEquals('y', $answer);
        $this->assertEquals(2, substr_count($result, 'Test prompt: '));
    }

    public function testAppendWithNoWrapOrWidth()
    {
        $console = new Console(null);
        $console->setWidth(0);
        $this->assertFalse($console->hasWrap());
        $this->assertFalse($console->hasWidth());
        $console->append('Hello World');

        ob_start();
        $console->send();
        $result = ob_get_clean();

        $this->assertEquals('    Hello World' . PHP_EOL, $result);
    }

    public function testSendWithHeaderNotYetSent()
    {
        $console = new Console();
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }
        $console->setHeader('My Header');
        $console->append('Body');

        ob_start();
        $console->send();
        $result = ob_get_clean();

        $this->assertStringContainsString('My Header', $result);
        $this->assertStringContainsString('Body', $result);
    }

    public function testSendWithFooter()
    {
        $console = new Console();
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }
        $console->setFooter('My Footer');
        $console->append('Body');

        ob_start();
        $console->send();
        $result = ob_get_clean();

        $this->assertStringContainsString('Body', $result);
        $this->assertStringContainsString('My Footer', $result);
    }

    public function testGetHeaderFormattedSingleLine()
    {
        $console = new Console();
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }
        $console->setHeader('Single Line Header', false);

        $this->assertEquals('    Single Line Header' . PHP_EOL, $console->getHeader(true));
    }

    public function testDisplayHelpFourthColor()
    {
        $command = new Command(name: 'user edit', params: '<id> --verbose', help: 'Edit a user.');
        $console  = new Console(80, '    ');
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }
        $console->setHelpColors(Color::BOLD_BLUE, Color::YELLOW, Color::BOLD_MAGENTA, Color::BOLD_CYAN);
        $console->addCommand($command);

        ob_start();
        $console->help();
        $result = ob_get_clean();

        $this->assertStringContainsString("\x1b[1;36m--verbose\x1b[0m", $result);
    }

    protected function runInSubprocess(string $code, string $stdin): array
    {
        $autoload       = dirname(__DIR__) . '/vendor/autoload.php';
        $descriptorSpec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open([PHP_BINARY, '-r', $code, $autoload], $descriptorSpec, $pipes);

        fwrite($pipes[0], $stdin);
        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);

        return [$stdout, $stderr, $exitCode];
    }

    public function testPromptReadsFromRealStdin()
    {
        $code = 'require $argv[1]; $c = new \Pop\Console\Console(); fwrite(STDOUT, $c->prompt("Prompt: "));';
        [$stdout, $stderr, $exitCode] = $this->runInSubprocess($code, "hello\n");

        $this->assertStringEndsWith('hello', $stdout, $stderr);
        $this->assertEquals(0, $exitCode, $stderr);
    }

    public function testConfirmNoExitsWithCode127()
    {
        $code = 'require $argv[1]; $c = new \Pop\Console\Console(); $c->confirm();';
        [$stdout, $stderr, $exitCode] = $this->runInSubprocess($code, "n\n");

        $this->assertEquals(127, $exitCode, $stderr);
    }

}
