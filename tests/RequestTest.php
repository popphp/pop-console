<?php

namespace Pop\Console\Test;

use Pop\Console\Request;
use Pop\Console\Input;

class RequestTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $request = new Request();
        $this->assertInstanceOf('Pop\Console\Request', $request);
    }

    public function testConstructorException()
    {
        $this->setExpectedException('Pop\Console\Exception');
        unset($_SERVER['argv']);
        $request = new Request();
    }

    public function testGetEnv()
    {
        $GLOBALS['_ENV']['SOME_VAR'] = 123;
        $request = new Request();
        $this->assertEquals(123, $request->getEnv('SOME_VAR'));
        $this->assertGreaterThanOrEqual(1, count($request->getEnvs()));
    }


    public function testGetOptionsAndCommands()
    {
        $request = new Request();
        $this->assertFalse($request->hasOption('-l'));
        $this->assertNull($request->getOption('-l'));
        $this->assertFalse($request->hasCommand('help'));
        $this->assertNull($request->getCommand('help'));
        $this->assertEquals(0, count($request->getOptions()));
        $this->assertEquals(0, count($request->getCommands()));
    }

    public function testGetScriptName()
    {
        $request = new Request();
        $this->assertContains('phpunit', $request->getScriptName());
    }

    public function testParse()
    {
        $_SERVER['argv'][] = 'help';
        $_SERVER['argv'][] = '--print';
        $_SERVER['argv'][] = 'page1';

        $commands = [
            new Input\Command('help'),
            new Input\Command('list'),
            new Input\Command('print')
        ];
        $options = [
            new Input\Option('-h'),
            new Input\Option('--list'),
            new Input\Option('-p|--print', Input\Option::VALUE_REQUIRED)
        ];
        $request = new Request();
        $request->parse($commands, $options);
        $this->assertEquals('page1', $request->getArguments()[2]);
    }

}