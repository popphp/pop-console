pop-console
===========

[![Build Status](https://travis-ci.org/popphp/pop-console.svg?branch=master)](https://travis-ci.org/popphp/pop-console)
[![Coverage Status](http://cc.popphp.org/coverage.php?comp=pop-console)](http://cc.popphp.org/pop-console/)

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

### Outputting to the console

You can use a console object to manage and deploy output to the console, including
a prepended header and appended footer.

```php
$console = new Pop\Console\Console();
$console->setHeader('My Application');
$console->setFooter('The End');

$console->append('Here is some console information.');
$console->append('Hope you enjoyed it!');

$console->send();
```

The above will output:

```text

    My Application
    
    Here is some console information.
    Hope you enjoyed it!

    The End

```

### Console colors

On consoles that support it, you can colorize text outputted to the console with the
``colorize()`` method:


```php
$console->append(
    'Here is some ' . 
    $console->colorize('IMPORTANT', Console::BOLD_RED) .
    ' console information.'
);
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

### Help screen

You can register commands with the console object to assist in auto-generating
a well-formatted, colorized help screen.

```php
use Pop\Console\Console;
use Pop\Console\Command;

$edit = new Command(
    'user edit', '<id>', 'This is the help for the user edit command'
);

$remove = new Command(
    'user remove', '<id>', 'This is the help for the user remove command'
);

$console = new Console();
$console->addCommand($edit);
$console->addCommand($remove);
$console->setHelpColors(
    Console::BOLD_CYAN,
    Console::BOLD_GREEN,
    Console::BOLD_YELLOW
);
```

Once the commands are registered with the main `$console` object, we can generate
the help screen like this: 

```php
$console->help();
```

The above command will output an auto-generated, colorized help screen with the commands
that are registered with the console object.

#### Note

These are basic examples. Ideally, you could wire an application to use the console
but not for setting routes, controllers and actions. Refer to the
[Pop PHP Tutorial](https://github.com/popphp/popphp-tutorial) example application
to see how to wire up a CLI-based application using Pop PHP.
