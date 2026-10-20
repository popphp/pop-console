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

    public function testRenderBumpsHelpOfCommandTooLongForHelpColumn()
    {
        $short = new Command(name: 'help', help: 'Show the help screen');
        $long  = new Command(
            name: 'fail:set', params: '[--system=] [--operation=] [--answer=] [--delay=] [--times=]', help: 'Set a failure'
        );
        $help  = new Help();

        $result = $help->render([$short, $long], false, null, '    ', 80, []);

        $this->assertEquals(
            '    help    Show the help screen' . PHP_EOL .
            PHP_EOL .
            '    fail:set [--system=] [--operation=] [--answer=] [--delay=] [--times=]' . PHP_EOL .
            '            Set a failure' . PHP_EOL,
            $result
        );
    }

    public function testRenderWrapsBumpedHelpAtHelpColumn()
    {
        $short = new Command(name: 'list', help: 'List things');
        $long  = new Command(
            name: 'this:is:a:very:long:command:name:with:no:options:at:all:really',
            help: 'This help text is long enough that it has to wrap across more than one line.'
        );
        $last  = new Command(name: 'show', help: 'Show a thing');
        $help  = new Help();

        $result = $help->render([$short, $long, $last], false, null, '    ', 60, []);

        $this->assertEquals(
            '    list    List things' . PHP_EOL .
            PHP_EOL .
            '    this:is:a:very:long:command:name:with:no:options:at:all:really' . PHP_EOL .
            '            This help text is long enough that it has to wrap' . PHP_EOL .
            '            across more than one line.' . PHP_EOL .
            PHP_EOL .
            '    show    Show a thing' . PHP_EOL,
            $result
        );
    }

    public function testSetAndGetMinHelpWidth()
    {
        $help = new Help();
        $this->assertEquals(40, $help->getMinHelpWidth());
        $this->assertTrue($help->hasMinHelpWidth());

        $this->assertSame($help, $help->setMinHelpWidth(50));
        $this->assertEquals(50, $help->getMinHelpWidth());

        $help->setMinHelpWidth(0);
        $this->assertFalse($help->hasMinHelpWidth());
    }

    public function testRenderUsesMinHelpWidthToDecideWhenToBump()
    {
        $short  = new Command(name: 'short', help: 'Short help');
        $medium = new Command(name: 'medium:command', params: '[--option-one=] [--two=]', help: 'Medium help');

        // 'medium:command [--option-one=] [--two=]' is 39 chars: 39 + 4 + 40 > 80 at the default
        $this->assertEquals(
            '    short    Short help' . PHP_EOL .
            PHP_EOL .
            '    medium:command [--option-one=] [--two=]' . PHP_EOL .
            '             Medium help' . PHP_EOL,
            (new Help())->render([$short, $medium], false, null, '    ', 80, [])
        );

        // ...but 39 + 4 + 30 <= 80, so it fits and sets the column
        $this->assertEquals(
            '    short' . str_repeat(' ', 34) . '    Short help' . PHP_EOL .
            '    medium:command [--option-one=] [--two=]    Medium help' . PHP_EOL,
            (new Help())->setMinHelpWidth(30)->render([$short, $medium], false, null, '    ', 80, [])
        );
    }

    public function testSetAndGetHelpGap()
    {
        $help = new Help();
        $this->assertEquals('    ', $help->getHelpGap());
        $this->assertTrue($help->hasHelpGap());

        $this->assertSame($help, $help->setHelpGap('  '));
        $this->assertEquals('  ', $help->getHelpGap());

        $help->setHelpGap('');
        $this->assertFalse($help->hasHelpGap());
    }

    public function testRenderUsesHelpGap()
    {
        $short = new Command(name: 'help', help: 'Show the help screen');
        $long  = new Command(
            name: 'fail:set', params: '[--system=] [--operation=] [--answer=] [--delay=] [--times=]', help: 'Set a failure'
        );

        $result = (new Help())->setHelpGap(' :: ')->render([$short, $long], false, null, '    ', 80, []);

        $this->assertEquals(
            '    help :: Show the help screen' . PHP_EOL .
            PHP_EOL .
            '    fail:set [--system=] [--operation=] [--answer=] [--delay=] [--times=]' . PHP_EOL .
            '            Set a failure' . PHP_EOL,
            $result
        );
    }

    public function testRenderHelpGapCountsTowardsMinHelpWidth()
    {
        $short  = new Command(name: 'short', help: 'Short help');
        $medium = new Command(name: 'medium:command', params: '[--option-one=] [--two=]', help: 'Medium help');

        // 39 + 1 + 40 = 80 fits within a wrap of 80 with a 1-char gap, where the default 4-char gap wouldn't
        $this->assertEquals(
            '    short' . str_repeat(' ', 34) . ' Short help' . PHP_EOL .
            '    medium:command [--option-one=] [--two=] Medium help' . PHP_EOL,
            (new Help())->setHelpGap(' ')->render([$short, $medium], false, null, '    ', 80, [])
        );
    }

    public function testRenderTooLongCommandWithoutHelpPrintsLabelOnly()
    {
        $short = new Command(name: 'help', help: 'Show the help screen');
        $long  = new Command(name: 'fail:set', params: '[--system=] [--operation=] [--answer=] [--delay=] [--times=]');
        $help  = new Help();

        $result = $help->render([$short, $long], false, null, '    ', 80, []);

        $this->assertEquals(
            '    help    Show the help screen' . PHP_EOL .
            '    fail:set [--system=] [--operation=] [--answer=] [--delay=] [--times=]' . PHP_EOL,
            $result
        );
    }

    public function testRenderWithNullWrapKeepsHelpOnOneLine()
    {
        $short = new Command(name: 'help', help: 'Show the help screen');
        $long  = new Command(
            name: 'fail:set', params: '[--system=] [--operation=] [--answer=] [--delay=] [--times=]', help: 'Set a failure'
        );
        $help  = new Help();

        $result = $help->render([$short, $long], false, null, '    ', null, []);

        $this->assertEquals(
            '    help' . str_repeat(' ', 65) . '    Show the help screen' . PHP_EOL .
            '    fail:set [--system=] [--operation=] [--answer=] [--delay=] [--times=]    Set a failure' . PHP_EOL,
            $result
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

    public function testConsoleDisplayHelpIncludesHeaderAndFooter()
    {
        $command = new Command(name: 'hello', help: 'This is the help');
        $console = new Console(80, '    ');
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }
        $console->setHeader('My Header');
        $console->setFooter('My Footer');
        $console->addCommand($command);

        ob_start();
        $console->help();
        $result = ob_get_clean();

        $this->assertStringContainsString('My Header', $result);
        $this->assertStringContainsString('hello', $result);
        $this->assertStringContainsString('My Footer', $result);
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

    public function testRenderWithoutFirstHelpColorLeavesSingleWordNameUncolored()
    {
        $command = new Command(name: 'hello', params: '-v', help: 'Hello help');
        $help    = new Help();

        $result = $help->render([$command], false, null, '    ', 80, [2 => Color::BOLD_MAGENTA]);

        // The name has no escape code prefix (helpColors[0] isn't set), even though the params
        // that follow it do get colorized (helpColors[2] is set) — proves colorizeHelpName()'s
        // final fallback branch (no color configured for a single-word name) ran.
        $this->assertTrue(str_starts_with($result, '    hello'));
        $this->assertStringContainsString("\x1b[1;35m-v\x1b[0m", $result);
    }

}
