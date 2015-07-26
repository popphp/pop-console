pop-console
===========

[![Build Status](https://travis-ci.org/popphp/pop-console.svg?branch=master)](https://travis-ci.org/popphp/pop-console)
[![Coverage Status](http://www.popphp.org/cc/coverage.php?comp=pop-console)](http://www.popphp.org/cc/pop-console/)

OVERVIEW
--------
`pop-console` provides a layer to run an application from the console window.
It has support for input commands and options as well ANSI-based console colors.
It can be easily be used with an application built with Pop to route requests
from the CLI to the application.

`pop-console` is a component of the [Pop PHP Framework](http://www.popphp.org/).

INSTALL
-------

Install `pop-console` using Composer.

    composer require popphp/pop-console


BASIC USAGE
-----------

In this simple example, we create a script called `pop` and wire it up. First,
we'll create some commands and an option and add them to the console object:

```php
use Pop\Console\Console;
use Pop\Console\Input;

$name = new Input\Option('-n|--name', Input\Option::VALUE_REQUIRED);

$help = new Input\Command('help');
$help->setHelp('This is the general help screen.');

$edit = new Input\Command('edit', Input\Command::VALUE_REQUIRED);
$edit->setHelp('This is the help screen for the edit command.');

$console = new Console();
$console->addOption($name);
$console->addCommand($help);
$console->addCommand($edit);
```

Once the commands and options are registered with the main `$console` object, we
can parse the incoming CLI request, check if it's valid and correctly route it:

```php
$console->parseRequest();

if ($console->isRequestValid()) {
    if ($console->request()->hasCommand('edit')) {
        $value = $console->getCommand('edit')->getValue();
        switch ($value) {
            case 'help':
                $help = $console->colorize(
                    $console->getCommand('edit')->getHelp(), Console::BOLD_YELLOW
                );
                $console->write($help, '    ');
                $console->send();
                break;
            default:
                $console->write('You have selected to edit ' . $value, '    ');
                if ($console->request()->hasOption('--name')) {
                    $console->write(
                        'You have added the name option of ' . $console->getOption('--name')->getValue(),
                        '    '
                    );
                }
                $console->send();
        }
    } else if ($console->request()->hasCommand('help')) {
        $help = $console->colorize(
            $console->getCommand('help')->getHelp(), Console::BOLD_YELLOW
        );
        $console->write($help, '    ');
        $console->send();
    } else {
        $console->write(
            $console->colorize('The command was not recognized.', Console::BOLD_RED),
            '    '
        );
        $console->send();
    }
} else {
    $console->write(
        $console->colorize('The command was not valid.', Console::BOLD_RED),
        '    '
    );
    $console->send();
}
```

Then, we can run the following valid commands:

    ./pop help
    This is the general help screen.
    
    ./pop edit users
    You have selected to edit users
    
    ./pop edit users --name=bob
    You have selected to edit users
    You have added the name option of bob
    
    ./pop edit help
    This is the help screen for the edit command.

And, any of these invalid commands will produce the error output:

    ./pop badcommand
    This is the help screen for the edit command.
    
    ./pop edit
    This is the help screen for the edit command.

The last example is not value because we made the argument value of
the `edit` command required.

This is a pretty barebones example. Ideally, you could wire an application to
use the console but setting routes, controllers and actions. Refer to the
[Pop PHP Skeleton](https://github.com/popphp/popphp-skeleton) example application
to see how to wire up a CLI-based application using Pop PHP.
