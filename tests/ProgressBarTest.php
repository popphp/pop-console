<?php

namespace Pop\Console\Test;

use Pop\Console\Console;
use Pop\Console\ProgressBar;
use Pop\Console\Color;
use PHPUnit\Framework\TestCase;

class ProgressBarTest extends TestCase
{

    public function testConstructorDefaults()
    {
        $bar = new ProgressBar(100);
        $this->assertEquals(0, $bar->getCurrent());
        $this->assertEquals(100, $bar->getTotal());
        $this->assertFalse($bar->isFinished());
    }

    public function testConstructorWithZeroTotalThrowsException()
    {
        $this->expectException('Pop\Console\Exception');
        new ProgressBar(0);
    }

    public function testConstructorWithNegativeTotalThrowsException()
    {
        $this->expectException('Pop\Console\Exception');
        new ProgressBar(-5);
    }

    public function testAdvanceIncrementsCurrent()
    {
        $bar = new ProgressBar(10);
        ob_start();
        $bar->advance();
        $bar->advance(2);
        ob_get_clean();

        $this->assertEquals(3, $bar->getCurrent());
    }

    public function testAdvancePastTotalClampsAtTotal()
    {
        $bar = new ProgressBar(10);
        ob_start();
        $bar->advance(50);
        ob_get_clean();

        $this->assertEquals(10, $bar->getCurrent());
    }

    public function testSetProgressClampsToZeroAndTotal()
    {
        $bar = new ProgressBar(10);

        ob_start();
        $bar->setProgress(-5);
        ob_get_clean();
        $this->assertEquals(0, $bar->getCurrent());

        ob_start();
        $bar->setProgress(999);
        ob_get_clean();
        $this->assertEquals(10, $bar->getCurrent());
    }

    public function testFinishSetsCurrentToTotalAndMarksFinished()
    {
        $bar = new ProgressBar(10);
        ob_start();
        $bar->advance(3);
        $bar->finish();
        ob_get_clean();

        $this->assertEquals(10, $bar->getCurrent());
        $this->assertTrue($bar->isFinished());
    }

    public function testRenderedOutputShape()
    {
        $bar = new ProgressBar(100, 'kettle build', 20);

        ob_start();
        $bar->setProgress(50);
        $output = ob_get_clean();

        $output = ltrim($output, "\r");

        $this->assertStringStartsWith('kettle build [', $output);
        $this->assertStringContainsString(']  50% (50/100)', $output);
    }

    public function testSetIndentPrefixesOutput()
    {
        $bar = new ProgressBar(10);
        $bar->setIndent('    ');

        ob_start();
        $bar->setProgress(1);
        $output = ob_get_clean();

        $this->assertStringStartsWith("\r    ", $output);
    }

    public function testConsoleProgressBarReturnsInstanceWithIndentApplied()
    {
        $console = new Console(80, '    ');
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }

        $bar = $console->progressBar(10, 'test');
        $this->assertInstanceOf('Pop\Console\ProgressBar', $bar);

        ob_start();
        $bar->setProgress(1);
        $output = ob_get_clean();

        $this->assertStringStartsWith("\r    test", $output);
    }

    public function testExactOutputAtHalfProgress()
    {
        $bar = new ProgressBar(10, null, 10);

        ob_start();
        $bar->setProgress(5);
        $output = ob_get_clean();

        $this->assertEquals("\r[====>     ]  50% (5/10)  ", $output);
    }

    public function testZeroProgressRendersAllEmptyChars()
    {
        $bar = new ProgressBar(10, null, 10);

        ob_start();
        $bar->setProgress(0);
        $output = ob_get_clean();

        $this->assertEquals("\r[          ]   0% (0/10)  ", $output);
    }

    public function testFullProgressRendersAllBarCharsWithNoProgressChar()
    {
        $bar = new ProgressBar(10, null, 10);

        ob_start();
        $bar->setProgress(10);
        $output = ob_get_clean();

        $this->assertEquals("\r[==========] 100% (10/10)  ", $output);
        $this->assertStringNotContainsString('>', $output);
    }

    public function testNoMessageOutputHasNoLeadingLabel()
    {
        $bar = new ProgressBar(10);

        ob_start();
        $bar->advance();
        $output = ob_get_clean();

        $this->assertStringStartsWith("\r[", $output);
    }

    public function testSetMessageIsFluentAndAffectsOutput()
    {
        $bar    = new ProgressBar(10, null, 10);
        $result = $bar->setMessage('uploading');

        $this->assertSame($bar, $result);

        ob_start();
        $bar->setProgress(1);
        $output = ob_get_clean();

        $this->assertStringStartsWith("\ruploading [", $output);
    }

    public function testSetWidthChangesBarLength()
    {
        $bar    = new ProgressBar(10, null, 10);
        $result = $bar->setWidth(5);

        $this->assertSame($bar, $result);

        ob_start();
        $bar->setProgress(5);
        $output = ob_get_clean();

        $this->assertEquals("\r[==>  ]  50% (5/10)  ", $output);
    }

    public function testSetCharsUsesCustomCharacters()
    {
        $bar    = new ProgressBar(10, null, 10);
        $result = $bar->setChars('#', '*', '.');

        $this->assertSame($bar, $result);

        ob_start();
        $bar->setProgress(5);
        $output = ob_get_clean();

        $this->assertEquals("\r[####*.....]  50% (5/10)  ", $output);
    }

    public function testSetColorWrapsBarInAnsiCode()
    {
        $bar    = new ProgressBar(10, null, 10);
        $result = $bar->setColor(Color::BOLD_GREEN);

        $this->assertSame($bar, $result);

        ob_start();
        $bar->setProgress(5);
        $output = ob_get_clean();

        $expectedBar = Color::colorize('====>     ', Color::BOLD_GREEN, null);
        $this->assertEquals("\r[" . $expectedBar . "]  50% (5/10)  ", $output);
    }

    public function testPercentRoundsForNonExactFraction()
    {
        $bar = new ProgressBar(3, null, 10);

        ob_start();
        $bar->setProgress(1);
        $output = ob_get_clean();

        $this->assertStringContainsString(' 33% (1/3)', $output);
    }

    public function testIsFinishedRemainsFalseUntilFinishIsCalled()
    {
        $bar = new ProgressBar(10);

        ob_start();
        $bar->advance(5);
        ob_get_clean();

        $this->assertFalse($bar->isFinished());

        ob_start();
        $bar->finish();
        ob_get_clean();

        $this->assertTrue($bar->isFinished());
    }

    public function testFluentSettersAllReturnSameInstance()
    {
        $bar = new ProgressBar(10);

        $this->assertSame($bar, $bar->setIndent(''));

        ob_start();
        $this->assertSame($bar, $bar->advance(0));
        $this->assertSame($bar, $bar->setProgress(0));
        ob_get_clean();
    }

}
