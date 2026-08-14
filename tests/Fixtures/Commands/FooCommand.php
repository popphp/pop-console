<?php

namespace Pop\Console\Test\Fixtures\Commands;

use Pop\Console\Command\AbstractCommand;

class FooCommand extends AbstractCommand
{

    public function __construct()
    {
        parent::__construct(name: 'foo:bar', params: '<id>', help: 'Foo bar help');
    }

}
