<?php

namespace Pop\Console\Test;

use Pop\Console\Console;
use Pop\Console\Table;
use Pop\Console\Color;
use PHPUnit\Framework\TestCase;

class TableTest extends TestCase
{

    public function testConstructorAndRender()
    {
        $table = new Table(['Name', 'Status'], [['kettle', 'active'], ['brew', 'idle']]);

        $expected = '+--------+--------+' . PHP_EOL .
            '| Name   | Status |' . PHP_EOL .
            '+--------+--------+' . PHP_EOL .
            '| kettle | active |' . PHP_EOL .
            '| brew   | idle   |' . PHP_EOL .
            '+--------+--------+' . PHP_EOL;

        $this->assertEquals($expected, $table->render());
    }

    public function testAddRow()
    {
        $table = new Table(['Name']);
        $table->addRow(['kettle']);
        $table->addRow(['brew']);

        $expected = '+--------+' . PHP_EOL .
            '| Name   |' . PHP_EOL .
            '+--------+' . PHP_EOL .
            '| kettle |' . PHP_EOL .
            '| brew   |' . PHP_EOL .
            '+--------+' . PHP_EOL;

        $this->assertEquals($expected, $table->render());
    }

    public function testSetRows()
    {
        $table = new Table(['Name']);
        $table->setRows([['kettle'], ['brew']]);

        $this->assertStringContainsString('kettle', $table->render());
        $this->assertStringContainsString('brew', $table->render());
    }

    public function testRaggedRowIsPaddedWithBlanks()
    {
        $table = new Table(['Name', 'Status'], [['kettle']]);

        $expected = '+--------+--------+' . PHP_EOL .
            '| Name   | Status |' . PHP_EOL .
            '+--------+--------+' . PHP_EOL .
            '| kettle |        |' . PHP_EOL .
            '+--------+--------+' . PHP_EOL;

        $this->assertEquals($expected, $table->render());
    }

    public function testNoHeadersOmitsHeaderRow()
    {
        $table = new Table([], [['a', 'b']]);

        $expected = '+---+---+' . PHP_EOL .
            '| a | b |' . PHP_EOL .
            '+---+---+' . PHP_EOL;

        $this->assertEquals($expected, $table->render());
    }

    public function testCustomBorderChars()
    {
        $table = new Table(['Name'], [['kettle']], '=', '#');

        $expected = '+========+' . PHP_EOL .
            '# Name   #' . PHP_EOL .
            '+========+' . PHP_EOL .
            '# kettle #' . PHP_EOL .
            '+========+' . PHP_EOL;

        $this->assertEquals($expected, $table->render());
    }

    public function testNullVerticalOmitsDividers()
    {
        $table = new Table([], [['a', 'bb'], ['ccc', 'd']], '-', null);

        $expected = '----------' . PHP_EOL .
            ' a     bb ' . PHP_EOL .
            ' ccc   d  ' . PHP_EOL .
            '----------' . PHP_EOL;

        $this->assertEquals($expected, $table->render());
    }

    public function testSetHeaderColorAppliesAnsiCodeAroundHeaderCellOnly()
    {
        $table = new Table(['Name'], [['kettle']]);
        $table->setHeaderColor(Color::BOLD_GREEN);

        $rendered = $table->render();
        $expectedHeaderCell = Color::colorize('Name  ', Color::BOLD_GREEN, null);

        $this->assertStringContainsString($expectedHeaderCell, $rendered);
        $this->assertStringNotContainsString(Color::colorize('kettle', Color::BOLD_GREEN, null), $rendered);
    }

    public function testEmptyTableRendersEmptyString()
    {
        $table = new Table();
        $this->assertEquals('', $table->render());
    }

    public function testConsoleTableReturnsString()
    {
        $console = new Console(80, '    ');
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }

        $result = $console->table(['Name'], [['kettle']], '-', '|', null, null, false, true);

        $expected = '    +--------+' . PHP_EOL .
            '    | Name   |' . PHP_EOL .
            '    +--------+' . PHP_EOL .
            '    | kettle |' . PHP_EOL .
            '    +--------+' . PHP_EOL;

        $this->assertEquals($expected, $result);
    }

    public function testConsoleTableEchoes()
    {
        $console = new Console(80, '    ');
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }

        ob_start();
        $result = $console->table(['Name'], [['kettle']]);
        $output = ob_get_clean();

        $this->assertInstanceOf('Pop\Console\Console', $result);
        $this->assertStringContainsString('| Name   |', $output);
        $this->assertStringContainsString('| kettle |', $output);
    }

}
