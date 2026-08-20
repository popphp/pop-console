<?php
declare(strict_types=1);

namespace Pop\Console\Test;

use Pop\Console\Console;
use Pop\Console\Command;
use Pop\Console\Color;
use Pop\Console\Help;
use PHPUnit\Framework\TestCase;

class HelpTest extends TestCase
{

    public function testRenderSingleCommand()
    {
        $command = new Command(name: 'hello', params: '-v', help: 'This is the help');
        $help    = new Help();

        $result = $help->render([$command], false, null, '    ', 80, []);

        $this->assertEquals('    hello -v    This is the help' . PHP_EOL, $result);
    }

    public function testRenderAddsBlankLineAfterWrappedNonLastCommand()
    {
        $wrapped = new Command(
            name: 'wrapped',
            help: 'This help text is long enough that it will wrap across multiple lines given a narrow wrap width.'
        );
        $short = new Command(name: 'short', help: 'Short help.');
        $help  = new Help();

        $result = $help->render([$wrapped, $short], false, null, '    ', 40, []);

        $wrappedPos = strpos($result, 'wrapped');
        $shortPos   = strpos($result, 'short');

        $this->assertNotFalse($wrappedPos);
        $this->assertNotFalse($shortPos);
        $this->assertStringContainsString(
            PHP_EOL . PHP_EOL, substr($result, $wrappedPos, $shortPos - $wrappedPos)
        );
    }

    public function testRenderWithSubCommandFiltersToMatchingNamespace()
    {
        $dbMigrate  = new Command(name: 'db:migrate', help: 'Migrate the database.');
        $userDbSync = new Command(name: 'userdb:sync', help: 'Sync the user database.');
        $help       = new Help();

        $result = $help->render([$dbMigrate, $userDbSync], false, 'db:', '    ', 80, []);

        $this->assertStringContainsString('db:migrate', $result);
        $this->assertStringNotContainsString('userdb:sync', $result);
    }

    public function testRenderWithSubCommandDoesNotMatchScriptNameCollision()
    {
        $dbMigrate = (new Command(name: 'dbapp db:migrate', help: 'Migrate the database.'))->setScriptName('dbapp');
        $userList  = (new Command(name: 'dbapp user:list', help: 'List users.'))->setScriptName('dbapp');
        $help      = new Help();

        $result = $help->render([$dbMigrate, $userList], false, 'db', '    ', 80, []);

        $this->assertStringContainsString('dbapp db:migrate', $result);
        $this->assertStringNotContainsString('dbapp user:list', $result);
    }

    public function testRenderWithSubCommandWithoutScriptNameStillMatchesOnBareName()
    {
        $userList  = new Command(name: 'user list', params: '-v', help: 'List users.');
        $userEdit  = new Command(name: 'user edit', params: '<id>', help: 'Edit a user.');
        $adminList = new Command(name: 'admin list', help: 'List admins.');
        $help      = new Help();

        $result = $help->render([$userList, $userEdit, $adminList], false, 'user', '    ', 80, []);

        $this->assertStringContainsString('user list', $result);
        $this->assertStringContainsString('user edit', $result);
        $this->assertStringNotContainsString('admin list', $result);
    }

    public function testRenderWithSubCommandMatchingNothingDoesNotThrow()
    {
        $command = new Command(name: 'db:migrate', help: 'Migrate the database.');
        $help    = new Help();

        $result = $help->render([$command], false, 'nope:', '    ', 80, []);

        $this->assertStringNotContainsString('db:migrate', $result);
    }

    public function testConsoleDisplayHelpDelegatesToHelp()
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

    public function testConsoleGetHelpForNamedCommandStillReturnsCommandHelp()
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

    public function testRenderWithHelpColors()
    {
        $userList   = new Command(name: 'user list', params: '-v --option=123 [<id>]', help: 'This is the users list command.');
        $userAdd    = new Command(name: 'user', params: '--name=');
        $userEdit   = new Command(name: 'user edit', params: '<id>', help: 'This is the users edit command.');
        $userDelete = new Command(name: 'user delete', params: '<id>', help: 'This is the users delete command. This is the users delete command. This is the users delete command. This is the users delete command. This is the users delete command.');
        $userShow   = new Command(name: 'user show', params: '-v --option=123 [<id>]', help: 'This is the users list command.');

        $help = new Help();

        $result = $help->render(
            [$userList, $userAdd, $userEdit, $userDelete, $userShow],
            false,
            null,
            '    ',
            80,
            [Color::BOLD_BLUE, Color::YELLOW, Color::BOLD_MAGENTA]
        );

        $this->assertStringContainsString('user', $result);
    }

    public function testRenderWithFourthHelpColor()
    {
        $command = new Command(name: 'user edit', params: '<id> --verbose', help: 'Edit a user.');
        $help    = new Help();

        $result = $help->render(
            [$command],
            false,
            null,
            '    ',
            80,
            [Color::BOLD_BLUE, Color::YELLOW, Color::BOLD_MAGENTA, Color::BOLD_CYAN]
        );

        $this->assertStringContainsString("\x1b[1;36m--verbose\x1b[0m", $result);
    }

    public function testRenderWithRawTrueSkipsColorEscapes()
    {
        $command = new Command(name: 'user edit', params: '<id> --verbose', help: 'Edit a user.');
        $help    = new Help();

        $result = $help->render(
            [$command],
            true,
            null,
            '    ',
            80,
            [Color::BOLD_BLUE, Color::YELLOW, Color::BOLD_MAGENTA, Color::BOLD_CYAN]
        );

        // When $raw = true, the result should NOT contain ANSI escape codes
        $this->assertStringNotContainsString("\x1b[", $result);
        // But should still contain the actual text
        $this->assertStringContainsString('user edit', $result);
        $this->assertStringContainsString('<id> --verbose', $result);
    }

}
