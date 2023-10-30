pop-console
===========

[![Build Status](https://github.com/popphp/pop-console/workflows/phpunit/badge.svg)](https://github.com/popphp/pop-console/actions)
[![Coverage Status](http://cc.popphp.org/coverage.php?comp=pop-console)](http://cc.popphp.org/pop-console/)

[![Join the chat at https://popphp.slack.com](https://media.popphp.org/img/slack.svg)](https://popphp.slack.com)
[![Join the chat at https://discord.gg/D9JBxPa5](https://media.popphp.org/img/discord.svg)](https://discord.gg/D9JBxPa5)

* [Overview](#overview)
* [Install](#install)
* [Quickstart](#quickstart)
* [Response Buffer](#response-buffer)
* [Colors](#colors)
* [Prompt](#prompt)
* [Commands](#commands)

Overview
--------
`pop-console` provides a layer to run an application from the console terminal.
It has support for input commands and options as well ANSI-based console colors.
It can be easily be used with an application built with Pop to route requests
from the CLI to the application.

`pop-console` is a component of the [Pop PHP Framework](http://www.popphp.org/).

**Note**

The code below is comprised of basic examples. Ideally, you could wire an application
to use the console but not for setting routes, controllers and actions. Refer to the
[Pop PHP Tutorial](https://github.com/popphp/popphp-tutorial) example application to see how to wire up a CLI-based application
complete with routes using Pop PHP.

Install
-------

Install `pop-console` using Composer.

    composer require popphp/pop-console

Or, require it in your composer.json file

    "require": {
        "popphp/pop-console" : "^4.0.0"
    }

[Top](#pop-console)

Quickstart
----------

### Outputting to the console

You can use a console object to manage and deploy output to the console, including
a prepended header and appended footer.

```php
use Pop\Console\Console;

$console = new Console();
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

### Enforcing a terminal width

The allowed text width can be enforced by passing the `$width` parameter to the constructor:

```php
use Pop\Console\Console;

$console = new Console(40);
$console->append('Here is some console information. This is a really long string. It will have to wrap.');
$console->send();
```

```text
    Here is some console information. This
    is a really long string. It will have to
    wrap.
```

### Setting an indent

By default, an indent of four spaces is set to provide a margin from the edge of the terminal. This can be adjusted
or turned by passing it to the constructor:

```php
use Pop\Console\Console;

$console = new Console(40, '  ');
$console->append(
    'Here is some console information using a 2 space indent. It will have to wrap.'
);
$console->send();
```

```text
  Here is some console information using a
  2 space indent. It will have to wrap.
```

[Top](#pop-console)

Response Buffer
---------------

### Append vs Write

In the above exmaples, the method `append()` was used in conjunction with `send()`. The method `append()`
appends the content to the response buffer, which will only get produced to the terminal screen when the
method `send()` is called. This is useful if you have to take a number of steps to create the response buffer
before sending it.

Using the method `write()` allows you to produce content to the terminal screen in real time, without
having to call the `send()` method. This is useful if you need to push something out to the terminal screen
at that time in the application.

```php
use Pop\Console\Console;

$console = new Console(40);
$console->write('Here is some console information. This is a really long string. It will have to wrap.');
```

### Newlines and Indents

By default, calling the `append()` or `write()` methods will produce the indent value at the beginning
of the content and a newline at the end of the content. If this is not the desired behavior, boolean flags
can be passed to control this:

```php
use Pop\Console\Console;

$console = new Console(40);
$console->write('Here ', false);          // No new line, but use indent
$console->write('is ', false, false);     // No new line, no indent
$console->write('some ', false, false);   // No new line, no indent
$console->write('content.', true, false); // Use new line, but no indent
```

```text
    Here is some content.
```

[Top](#pop-console)

Colors
------

On a console terminal that supports it, you can colorize text outputted to the console
with the ``colorize()`` method:

```php
$console->append(
    'Here is some ' . 
    $console->colorize('IMPORTANT', Console::BOLD_RED) .
    ' console information.'
);
```

Available color constants include:

- NORMAL
- BLACK
- RED
- GREEN
- YELLOW
- BLUE
- MAGENTA
- CYAN
- WHITE
- GRAY
- BOLD_RED
- BOLD_GREEN
- BOLD_YELLOW
- BOLD_BLUE
- BOLD_MAGENTA
- BOLD_CYAN
- BOLD_WHITE

[Top](#pop-console)

Prompt
------

You can trigger a prompt to get information from the user:

```php
use Pop\Console\Console;

$console = new Console();
$name    = $console->prompt('Please provide your name: ');
echo '    Hello ' . $name . '!' . PHP_EOL;
```

```bash
$ ./app
    Please provide your name:  Nick
    Hello Nick!
```

You can also enforce a certain set of options as well as case-sensitivity.
The prompt will not accept a value outside of the provided range of option
values. If the case-sensitive flag is set to `true`, the prompt will not
accept values that are not an exact case-match.

```php
use Pop\Console\Console;

$console = new Console();
$letter  = $console->prompt(
    'Which is your favorite letter: A, B, C, or D? ',
    ['A', 'B', 'C', 'D'],
    true
);
echo '    Your favorite letter is ' . $letter . '.' . PHP_EOL;
```

```bash
$ ./app
    Which is your favorite letter: A, B, C, or D? B
    Your favorite letter is B.
```


[Top](#pop-console)

Commands
--------

A command object allows you to define the name, parameters and help string values of a command
and add the command to the console object:

```php
use Pop\Console\Console;
use Pop\Console\Command;

$command1 = new Command('users');
$command1->setParams('--list [<id>]');
$command1->setHelp('This is the users help screen');

$command2 = new Command('roles');
$command2->setParams('--list [<id>]');
$command2->setHelp('This is the roles help screen');

$console = new Console();
$console->addCommand($command1);
$console->addCommand($command2);
```

[Top](#pop-console)

Help Screen
-----------

Registering the commands with the console object like in the above example allows you
to call the `help()` method to view the auto-generated help screen:

```php
$console->help();
```

```text
    users --list [<id>]    This is the users help screen
    roles --list [<id>]    This is the roles help screen
```

However, the console object has the method `addCommandsFromRoutes()` which works in conjunction
with a `Pop\Router\Cli\Match` object to automatically generate the command, along with their
parameters and help strings.

```php
use Pop\Console\Console;

$this->console->addCommandsFromRoutes($cliRouteMatch, './myapp');
```

This console will use the CLI route match object and parse out all of the commands
and make them available for the console object to leverage for the help screen.

### Help colors

An extra layer of presentation control is available by way of setting the help screen colors.
You can choose up to 4 colors that will be used in breaking apart the command strings by name
and parameters and colorizing them to make the different segments standout in an organized fashion.

Let's take a look at the abstract constructor of the `pop-kettle` component.

```php
    public function __construct(Application $application, Console $console)
    {
        $this->application = $application;
        $this->console     = $console;

        $this->console->setHelpColors(Console::BOLD_CYAN, Console::BOLD_GREEN, Console::BOLD_MAGENTA);
        $this->console->addCommandsFromRoutes($application->router()->getRouteMatch(), './kettle');
    }

    public function help()
    {
        $this->console->help();
    }
```

In the above constructor method, the application object pushes the CLI route match object into the
console method `addCommandsFromRoutes()`. The second parameter `./kettle` is a script prefix to prepend
to each line of help. Those two lines are all that is needed to produce the colorful and well organized
help screen for `pop-kettle`, which is called within the controller's `help()` method.

The output looks like this:

![Pop PDF Form](tests/tmp/console-help.jpg)

[Top](#pop-console)
