<?php

namespace Pop\Console\Test\Fixtures\CommandsWithInvalidEntries;

use Pop\Console\Command\AbstractCommand;

class QuxCommand extends AbstractCommand
{

    public function __construct()
    {
        parent::__construct(name: 'qux:corge', help: 'Qux corge help');
    }

}
