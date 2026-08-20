<?php

namespace Pop\Console\Test\Fixtures\CommandsWithInvalidEntries;

use Pop\Console\Command\AbstractCommand;

class NotBrokenCommand extends AbstractCommand
{

    public function __construct()
    {
        parent::__construct(name: 'broken:command');
    }

}
