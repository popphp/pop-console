<?php

namespace Pop\Console\Test\Fixtures\Commands;

use Pop\Console\Command\AbstractCommand;

class BazCommand extends AbstractCommand
{

    public function __construct()
    {
        parent::__construct(name: 'baz:qux', help: 'Baz qux help');
    }

}
