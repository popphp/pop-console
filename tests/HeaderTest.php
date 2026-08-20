<?php
declare(strict_types=1);

namespace Pop\Console\Test;

use Pop\Console\Console;
use Pop\Console\Header;
use PHPUnit\Framework\TestCase;

class HeaderTest extends TestCase
{

    public function testLine1()
    {
        $header = new Header('    ', 10);
        $result = $header->line();

        $this->assertEquals('    ----------' . PHP_EOL, $result);
    }

    public function testLine2()
    {
        $header = new Header('', null, 160, 0);
        $result = $header->line('-', null, true);

        $this->assertEquals(str_repeat('-', 160) . PHP_EOL, $result);
    }

    public function testHeader1()
    {
        $header = new Header('    ');
        $result = $header->header('Hello World');

        $this->assertEquals('    Hello World' . PHP_EOL . '    -----------' . PHP_EOL, $result);
    }

    public function testHeader2()
    {
        $header = new Header('    ', 20);
        $result = $header->header('Hello World', '-', 'auto', 'left', true);

        $this->assertEquals('    Hello World' . PHP_EOL . '    --------------------' . PHP_EOL, $result);
    }

    public function testHeader3()
    {
        $header = new Header('    ', 20);
        $result = $header->header('Hello World', '-', 'auto', 'right');

        $this->assertEquals('             Hello World' . PHP_EOL . '    --------------------' . PHP_EOL, $result);
    }

    public function testHeader4()
    {
        $header = new Header('', null, 160, 0);
        $result = $header->header('Hello World', '-', 'auto');

        $this->assertEquals('Hello World' . PHP_EOL . str_repeat('-', 160) . PHP_EOL, $result);
    }

    public function testHeader5()
    {
        $header = new Header('    ', 10);
        $result = $header->header('Hello World', '-', null, 'right');

        $this->assertEquals('         Hello' . PHP_EOL . '         World' . PHP_EOL . '    ----------' . PHP_EOL, $result);
    }

    public function testHeader6()
    {
        $header = new Header('', null, 160, 0);
        $result = $header->header('Hello World. This is a long string of text. This is a long string of text. This is a long string of text. This is a long string of text. This is a long string of text. This is a long string of text.', '-', null, 'center');

        $this->assertStringContainsString('This is a long string of text', $result);
    }

    public function testHeaderLeft()
    {
        $header = new Header('    ', 20);
        $result = $header->headerLeft('Hello World');

        $this->assertEquals('    Hello World' . PHP_EOL . '    --------------------' . PHP_EOL, $result);
    }

    public function testHeaderRight()
    {
        $header = new Header('    ', 20);
        $result = $header->headerRight('Hello World');

        $this->assertEquals('             Hello World' . PHP_EOL . '    --------------------' . PHP_EOL, $result);
    }

    public function testHeaderCenter()
    {
        $header = new Header('    ', 20);
        $result = $header->headerCenter('Hello World');

        $this->assertEquals('         Hello World' . PHP_EOL . '    --------------------' . PHP_EOL, $result);
    }

    public function testConsoleLineDelegatesToHeader()
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

    public function testConsoleHeaderDelegatesToHeader()
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

    public function testConsoleHeaderLeftDelegatesToHeader()
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

}
