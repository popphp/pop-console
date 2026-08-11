<?php

namespace Pop\Console\Test\Command;

use Pop\Console\Command\Exception;
use PHPUnit\Framework\TestCase;

class ExceptionTest extends TestCase
{

    public function testInstance()
    {
        $exception = new Exception('Error');
        $this->assertInstanceOf('Pop\Console\Command\Exception', $exception);
        $this->assertEquals('Error', $exception->getMessage());
    }

}
