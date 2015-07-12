pop-console
===========

[![Build Status](https://travis-ci.org/popphp/pop-console.svg?branch=master)](https://travis-ci.org/popphp/pop-console)

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

In this simple example, we create a script called `pop` and set the following:

```php
use Pop\Console\Console;
use Pop\Console\Input;

$console = new Console();
$console->addOption(new Input\Option('-l|--list'));
$console->addCommand(new Input\Command('help'));

$console->parseRequest();

if ($console->request()->hasArgument('help')) {
    $help = $console->colorize('This is the help screen.', Console::BOLD_YELLOW);
    $console->write($help);
    $console->send();
} else if ($console->request()->hasArgument('users')) {
    $console->write($console->colorize('You selected to list users.', Console::CYAN));
    $console->send();
}
```

Then, we run the following commands:

    ./pop --list users
    You selected to list users.
    
    ./pop help
    This is the help screen.

This is a pretty barebones example. Ideally, you could wire an application to
use the console but setting routes, controllers and actions. Refer to the
[Pop PHP Skeleton](https://github.com/popphp/popphp-skeleton) example application
to see how to wire up a console-based application.
