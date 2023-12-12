<?php

namespace Pop\Console\Test;

use Pop\Console\Color;
use PHPUnit\Framework\TestCase;

class ColorTest extends TestCase
{

    public function testColor()
    {
        $string = Color::colorize('Hello World', Color::BOLD_BLUE, Color::RED);
        $this->assertStringContainsString('[1;34m', $string);
        $this->assertStringContainsString('[41m', $string);
        $this->assertStringContainsString('[0m', $string);
    }

    public function testBadColor()
    {
        $string = Color::colorize('Hello World', 400, 500);
        $this->assertStringContainsString('Hello World', $string);
    }

}
