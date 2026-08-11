<?php

namespace Pop\Console\Test\Fixtures\Commands;

use Pop\Console\Command\AbstractCommand;

class FooCommand extends AbstractCommand
{

    public function __construct()
    {
        parent::__construct('foo:bar', '<id>', 'Foo bar help');
    }

}
