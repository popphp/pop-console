pop-console
===========

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
use Pop\Console\Command;

$edit = new Command('edit', Input\Command::VALUE_REQUIRED);
$edit->setHelp('This is the help screen for the edit command.');

$console = new Console();
$console->addCommand($help);
$console->addCommand($edit);
```

Once the commands are registered with the main `$console` object, we access
them like so:

```php
$console->write($console->help('edit'), '    ');
$console->send();
```

### Using a prompt

You can also trigger a prompt to get information from the user. You can enforce
a certain set of options as well as whether or not they are case-sensitive:

```php
$console = new Pop\Console\Console();
$letter  = $console->prompt(
    'Which is your favorite letter: A, B, C, or D? ',
    ['A', 'B', 'C', 'D'],
    true
);
echo 'Your favorite letter is ' . $letter . '.';
```

    ./pop
    Which is your favorite letter: A, B, C, or D? B   // <- User types 'B'
    Your favorite letter is B.

These are basic examples. Ideally, you could wire an application to use the console
but not for setting routes, controllers and actions. Refer to the
[Pop PHP Skeleton](https://github.com/popphp/popphp-skeleton) example application
to see how to wire up a CLI-based application using Pop PHP.
