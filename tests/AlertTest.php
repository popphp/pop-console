<?php
declare(strict_types=1);

namespace Pop\Console\Test;

use Pop\Console\Console;
use Pop\Console\Alert;
use PHPUnit\Framework\TestCase;

class AlertTest extends TestCase
{

    public function testAlert1()
    {
        $alert  = new Alert('    ', 20);
        $result = $alert->alertDanger('Hello World. This is a longer alert.');

        $this->assertTrue(str_contains($result, "\x1b[1;97m\x1b[101m    Hello World.    \x1b[0m"));
    }

    public function testAlert2()
    {
        $alert = new Alert('    ', null, 160, 0);
        $result = $alert->alertDanger(
            'Hello World. This is a longer alert. This is a longer alert. This is a longer alert. This is a longer alert. This is a longer alert. This is a longer alert. This is a longer alert. This is a longer alert. This is a longer alert. This is a longer alert.',
            null, 'center', 4, true);

        $this->assertTrue(str_contains($result, "\x1b[1;97m\x1b[101m"));
        $this->assertTrue(str_contains($result, "Hello World. This is a longer alert."));
    }

    public function testAlert3()
    {
        $alert  = new Alert('    ', 20);
        $result = $alert->alertDanger('Hello World.', 'auto');

        $this->assertTrue(str_contains($result, "\x1b[1;97m\x1b[101m    Hello World.    \x1b[0m"));
    }

    public function testAlert4()
    {
        $alert  = new Alert('    ', null, 160, 4);
        $result = $alert->alertDanger('Hello World.', 'auto');

        $this->assertTrue(str_contains($result, "\x1b[1;97m\x1b[101m"));
        $this->assertTrue(str_contains($result, "Hello World."));
    }

    public function testAlert5()
    {
        $alert  = new Alert('    ', null, 160, 4);
        $result = $alert->alertDanger('Hello World.', 'auto', 'left');

        $this->assertTrue(str_contains($result, "\x1b[1;97m\x1b[101m"));
        $this->assertTrue(str_contains($result, "Hello World."));
    }

    public function testAlert6()
    {
        $alert  = new Alert('    ', null, 160, 4);
        $result = $alert->alertDanger('Hello World.', 'auto', 'right');

        $this->assertTrue(str_contains($result, "\x1b[1;97m\x1b[101m"));
        $this->assertTrue(str_contains($result, "Hello World."));
    }

    public function testAlertDanger()
    {
        $alert  = new Alert('    ', 20);
        $result = $alert->alertDanger('Hello World');

        $this->assertTrue(str_contains($result, "\x1b[1;97m\x1b[101m    Hello World    \x1b[0m"));
    }

    public function testAlertWarning()
    {
        $alert  = new Alert('    ', 20);
        $result = $alert->alertWarning('Hello World');

        $this->assertTrue(str_contains($result, "\x1b[1;30m\x1b[103m    Hello World    \x1b[0m"));
    }

    public function testAlertSuccess()
    {
        $alert  = new Alert('    ', 20);
        $result = $alert->alertSuccess('Hello World');

        $this->assertTrue(str_contains($result, "\x1b[1;30m\x1b[42m    Hello World    \x1b[0m"));
    }

    public function testAlertInfo()
    {
        $alert  = new Alert('    ', 20);
        $result = $alert->alertInfo('Hello World');

        $this->assertTrue(str_contains($result, "\x1b[1;97m\x1b[104m    Hello World    \x1b[0m"));
    }

    public function testAlertPrimary()
    {
        $alert  = new Alert('    ', 20);
        $result = $alert->alertPrimary('Hello World');

        $this->assertTrue(str_contains($result, "\x1b[1;97m\x1b[44m    Hello World    \x1b[0m"));
    }

    public function testAlertSecondary()
    {
        $alert  = new Alert('    ', 20);
        $result = $alert->alertSecondary('Hello World');

        $this->assertTrue(str_contains($result, "\x1b[1;97m\x1b[45m    Hello World    \x1b[0m"));
    }

    public function testAlertDark()
    {
        $alert  = new Alert('    ', 20);
        $result = $alert->alertDark('Hello World');

        $this->assertTrue(str_contains($result, "\x1b[1;97m\x1b[100m    Hello World    \x1b[0m"));
    }

    public function testAlertLight()
    {
        $alert  = new Alert('    ', 20);
        $result = $alert->alertLight('Hello World');

        $this->assertTrue(str_contains($result, "\x1b[1;30m\x1b[47m    Hello World    \x1b[0m"));
    }

    public function testAlertBox1()
    {
        $alert  = new Alert('    ', 20);
        $result = $alert->alertBox('Hello World');

        $this->assertStringContainsString('Hello World', $result);
        $this->assertStringContainsString('|', $result);
    }

    public function testAlertBox2()
    {
        $alert  = new Alert('    ', 20);
        $result = $alert->alertBox(
            'Hello World. This is a longer alert. This is a longer alert.',
            '-', '|', null, 'center', 4, true
        );

        $this->assertTrue(str_contains($result, "   -------------------"));
        $this->assertTrue(str_contains($result, "    |   Hello World"));
    }

    public function testAlertBox3()
    {
        $alert  = new Alert('    ', null);
        $result = $alert->alertBox('Hello World. This is a longer alert. This is a longer alert. This is a longer alert. This is a longer alert. This is a longer alert. This is a longer alert. This is a longer alert. This is a longer alert. This is a longer alert. This is a longer alert.');

        $this->assertTrue(str_contains($result, "   -------------------"));
        $this->assertTrue(str_contains($result, "Hello World"));
    }

    public function testAlertBox4()
    {
        $alert  = new Alert('    ', 80);
        $result = $alert->alertBox('Hello World. This is a longer alert.', '-', '|', 'auto', 'center');

        $this->assertTrue(str_contains($result, "-------------------"));
        $this->assertTrue(str_contains($result, "Hello World"));
    }

    public function testAlertBox5()
    {
        $alert  = new Alert('    ', null, 160, 4);
        $result = $alert->alertBox('Hello World. This is a longer alert.', '-', '|', 'auto', 'right');

        $this->assertTrue(str_contains($result, "-------------------"));
        $this->assertTrue(str_contains($result, "Hello World"));
    }

    public function testAlertBox6()
    {
        $alert  = new Alert('    ', null, 160, 4);
        $result = $alert->alertBox('Hello World. This is a longer alert.', '-', '|', 'auto', 'left');

        $this->assertTrue(str_contains($result, "-------------------"));
        $this->assertTrue(str_contains($result, "Hello World"));
    }

    public function testConsoleAlertDangerDelegatesToAlert()
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

    public function testConsoleAlertBoxDelegatesToAlert()
    {
        $console = new Console(20);
        if (!$console->hasWidth()) {
            $console->setWidth(160)->setHeight(50);
        }

        ob_start();
        $console->alertBox('Hello World');
        $result = ob_get_clean();

        $this->assertStringContainsString('Hello World', $result);
    }

}
