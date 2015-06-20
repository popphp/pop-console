<?php

namespace Pop\Console\Test;

use Pop\Console\Response;

class ResponseTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $response = new Response('Hello World');
        $this->assertInstanceOf('Pop\Console\Response', $response);
        $this->assertEquals('Hello World', $response->getBody());
        $response->reset();
        $this->assertNull($response->getBody());
    }

}