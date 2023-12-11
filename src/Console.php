<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Console;

use Pop\Router\Match\Cli;
use ReflectionClass;

/**
 * Console class
 *
 * @category   Pop
 * @package    Pop\Console
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    4.0.0
 */
class Console
{

    /**
     * Console wrap
     * @var ?int
     */
    protected ?int $wrap = null;

    /**
     * Console indent
     * @var string
     */
    protected string $indent = '';

    /**
     * Console padding
     * @var array
     */
    protected array $padding = [
        'top'    => 0,
        'right'  => 0,
        'bottom' => 0,
        'left'   => 0
    ];

    /**
     * Console terminal width
     * @var int
     */
    protected int $width = 0;

    /**
     * Console terminal height
     * @var int
     */
    protected int $height = 0;

    /**
     * Console response body
     * @var ?string
     */
    protected ?string $response = null;

    /**
     * Commands
     * @var array
     */
    protected array $commands = [];

    /**
     * Console header
     * @var ?string
     */
    protected ?string $header = null;

    /**
     * Flag for if console header has been sent
     * @var bool
     */
    protected bool $headerSent = false;

    /**
     * Console footer
     * @var ?string
     */
    protected ?string $footer = null;

    /**
     * Help colors
     * @var array
     */
    protected array $helpColors = [];

    /**
     * SERVER array
     * @var array
     */
    protected array $server = [];

    /**
     * ENV array
     * @var array
     */
    protected array $env = [];

    /**
     * Instantiate a new console object
     *
     * @param  ?int    $wrap
     * @param  string  $indent
     * @param  ?array  $padding
     * @throws Exception
     */
    public function __construct(?int $wrap = null, string $indent = '', ?array $padding = null)
    {
        $height = null;
        $width  = null;

        if (function_exists('exec')) {
            if (empty(exec('which stty'))) {
                [$height, $width] = explode(' ', exec('stty size'), 2);
            } else if (!empty(exec('which tput'))) {
                $height = exec('tput lines');
                $width  = exec('tput cols');
            }
            if (!empty($height) && !empty($width)) {
                $this->setHeight($height);
                $this->setWidth($width);
            }
        }

        if ($wrap !== null) {
            $this->setWrap($wrap);
        }
        $this->setIndent($indent);
        if ($padding !== null) {
            $this->setPadding($padding);
        }

        $this->server = (isset($_SERVER)) ? $_SERVER : [];
        $this->env    = (isset($_ENV))    ? $_ENV    : [];
    }

    /**
     * Set the wrap width of the console object
     *
     * @param  int $wrap
     * @return Console
     */
    public function setWrap(int $wrap): Console
    {
        $this->wrap = (int)$wrap;
        return $this;
    }

    /**
     * Set the indentation of the console object
     *
     * @param  ?string $indent
     * @return Console
     */
    public function setIndent(?string $indent = null): Console
    {
        $this->indent = $indent;
        return $this;
    }

    /**
     * Set the terminal width of the console object
     *
     * @param  int $width
     * @return Console
     */
    public function setWidth(int $width): Console
    {
        $this->width = (int)$width;
        return $this;
    }

    /**
     * Set the terminal height of the console object
     *
     * @param  int $height
     * @return Console
     */
    public function setHeight(int $height): Console
    {
        $this->height = (int)$height;
        return $this;
    }

    /**
     * Set the terminal padding
     *
     * @param  array $padding
     * @throws Exception
     * @return Console
     */
    public function setPadding(array $padding): Console
    {
        if (!isset($padding['top']) || !isset($padding['right']) || !isset($padding['bottom']) || !isset($padding['left'])) {
            throw new Exception('Error: The padding parameters are set correctly (top, right, bottom and left.)');
        }
        $this->padding = $padding;
        return $this;
    }

    /**
     * Set the console header
     *
     * @param  string $header
     * @return Console
     */
    public function setHeader(string $header): Console
    {
        $this->header = $header;
        return $this;
    }

    /**
     * Set the console footer
     *
     * @param  string $footer
     * @return Console
     */
    public function setFooter(string $footer): Console
    {
        $this->footer = $footer;
        return $this;
    }

    /**
     * Set the console header sent flag
     *
     * @param  bool $headerSent
     * @return Console
     */
    public function setHeaderSent(bool $headerSent = true): Console
    {
        $this->headerSent = (bool)$headerSent;
        return $this;
    }

    /**
     * Set the console help colors
     *
     * @param  int  $color1
     * @param  ?int $color2
     * @param  ?int $color3
     * @param  ?int $color4
     * @return Console
     */
    public function setHelpColors(int $color1, ?int $color2 = null, ?int $color3 = null, ?int $color4 = null): Console
    {
        $this->helpColors = [
            $color1
        ];
        if ($color2 !== null) {
            $this->helpColors[] = $color2;
        }
        if ($color3 !== null) {
            $this->helpColors[] = $color3;
        }
        if ($color4 !== null) {
            $this->helpColors[] = $color4;
        }

        return $this;
    }

    /**
     * Get the wrap width of the console object
     *
     * @return int
     */
    public function getWrap(): int
    {
        return $this->wrap;
    }

    /**
     * Get the indentation of the console object
     *
     * @return ?string
     */
    public function getIndent(): ?string
    {
        return $this->indent;
    }

    /**
     * Get the terminal width of the console object
     *
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * Get the terminal height of the console object
     *
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * Get the terminal padding
     *
     * @param  ?string $key
     * @return array|int|string
     */
    public function getPadding(?string $key = null): array|int|string
    {
        return (($key !== null) && isset($this->padding[$key])) ? $this->padding[$key] : $this->padding;
    }

    /**
     * Has terminal width
     *
     * @return bool
     */
    public function hasWidth(): bool
    {
        return !empty($this->width);
    }

    /**
     * Has terminal height
     *
     * @return bool
     */
    public function hasHeight(): bool
    {
        return !empty($this->height);
    }

    /**
     * Has terminal padding
     *
     * @param  string $key
     * @return bool
     */
    public function hasPadding(string $key): bool
    {
        return !empty($this->padding[$key]);
    }

    /**
     * Get the console header
     *
     * @param  bool $formatted
     * @return ?string
     */
    public function getHeader(bool $formatted = false): ?string
    {
        return ($formatted) ? $this->formatTemplate($this->header) : $this->header;
    }

    /**
     * Get the console footer
     *
     * @param  bool $formatted
     * @return ?string
     */
    public function getFooter(bool $formatted = false): ?string
    {
        return ($formatted) ? $this->formatTemplate($this->footer) : $this->footer;
    }

    /**
     * Get the console header sent flag
     *
     * @return bool
     */
    public function getHeaderSent(): bool
    {
        return $this->headerSent;
    }

    /**
     * Get the console help colors
     *
     * @return array
     */
    public function getHelpColors(): array
    {
        return $this->helpColors;
    }

    /**
     * Get the console help colors
     *
     * @return array
     */
    public function getAvailableColors(): array
    {
        return (new ReflectionClass('Pop\Console\Color'))->getConstants();
    }

    /**
     * Get a value from $_SERVER, or the whole array
     *
     * @param  ?string $key
     * @return string|array|null
     */
    public function getServer(?string $key = null): string|array|null
    {
        if ($key === null) {
            return $this->server;
        } else {
            return $this->server[$key] ?? null;
        }
    }

    /**
     * Get a value from $_ENV, or the whole array
     *
     * @param  ?string $key
     * @return string|array|null
     */
    public function getEnv(?string $key = null): string|array|null
    {
        if ($key === null) {
            return $this->env;
        } else {
            return $this->env[$key] ?? null;
        }
    }

    /**
     * Add a command
     *
     * @param  Command $command
     * @return Console
     */
    public function addCommand(Command $command): Console
    {
        $this->commands[$command->getName()] = $command;
        return $this;
    }

    /**
     * Add commands
     *
     * @param  array $commands
     * @return Console
     */
    public function addCommands(array $commands): Console
    {
        foreach ($commands as $command) {
            $this->addCommand($command);
        }
        return $this;
    }

    /**
     * Get commands
     *
     * @return array
     */
    public function getCommands(): array
    {
        return $this->commands;
    }

    /**
     * Get a command
     *
     * @param  string $command
     * @return Command|null
     */
    public function getCommand(string $command): Command|null
    {
        return $this->commands[$command] ?? null;
    }

    /**
     * Check if the console object has a command
     *
     * @param  string $command
     * @return bool
     */
    public function hasCommand(string $command): bool
    {
        return isset($this->commands[$command]);
    }

    /**
     * Get commands from routes
     *
     * @param  Cli     $routeMatch
     * @param  ?string $scriptName
     * @return array
     */
    public function getCommandsFromRoutes(Cli $routeMatch, ?string $scriptName = null): array
    {
        $routeMatch->match();

        $commandRoutes = $routeMatch->getRoutes();
        $commands      = $routeMatch->getCommands();
        $commandsToAdd = [];

        foreach ($commands as $name => $command) {
            $commandName = implode(' ', $command);
            $params      = trim(substr((string)$name, strlen((string)$commandName)));
            $params      = (!empty($params)) ? $params : null;
            $help        = (isset($commandRoutes[$name]) && isset($commandRoutes[$name]['help'])) ?
                $commandRoutes[$name]['help'] : null;

            if ($scriptName !== null) {
                $commandName = $scriptName . ' ' . $commandName;
            }

            $commandsToAdd[] = new Command($commandName, $params, $help);
        }

        return $commandsToAdd;
    }

    /**
     * Add commands from routes
     *
     * @param  Cli     $routeMatch
     * @param  ?string $scriptName
     * @return Console
     */
    public function addCommandsFromRoutes(Cli $routeMatch, ?string $scriptName = null): Console
    {
        $commands = $this->getCommandsFromRoutes($routeMatch, $scriptName);

        if (!empty($commands)) {
            $this->addCommands($commands);
        }

        return $this;
    }

    /**
     * Get a help
     *
     * @param  ?string $command
     * @return string|null
     */
    public function help(?string $command = null): string|null
    {
        if ($command !== null) {
            return $this->commands[$command]?->getHelp();
        } else {
            $this->displayHelp();
            return null;
        }
    }

    /**
     * Get input from the prompt
     *
     * @param  string $prompt
     * @param  ?array $options
     * @param  bool   $caseSensitive
     * @param  int    $length
     * @param  bool   $withHeaders
     * @return string
     */
    public function prompt(
        string $prompt, ?array $options = null, bool $caseSensitive = false, int $length = 500, bool $withHeaders = true
    ): string
    {
        if (($withHeaders) && ($this->header !== null)) {
            $this->headerSent = true;
            echo $this->formatTemplate($this->header) . $this->indent . $prompt;
        } else {
            echo $this->indent . $prompt;
        }

        $input = null;

        /**
         * $_SERVER['X_POP_CONSOLE_INPUT'] is for testing purposes only
         */
        if ($options !== null) {
            $length = 0;
            foreach ($options as $key => $value) {
                $options[$key] = ($caseSensitive) ? $value : strtolower((string)$value);
                if (strlen((string)$value) > $length) {
                    $length = strlen((string)$value);
                }
            }

            while (!in_array($input, $options)) {
                if ($input !== null) {
                    echo $this->indent . $prompt;
                }
                $input = $this->getPromptInput($prompt, $length, $caseSensitive);
            }
        } else {
            while ($input === null) {
                $input = $this->getPromptInput($prompt, $length, $caseSensitive);
            }
        }

        return $input;
    }

    /**
     * Append a string of text to the response body
     *
     * @param  ?string $text
     * @param  bool    $newline
     * @param  bool    $indent
     * @return Console
     */
    public function append(?string $text = null, bool $newline = true, bool $indent = true): Console
    {
        if ($this->wrap != 0) {
            $lines = (strlen((string)$text) > $this->wrap) ?
                explode(PHP_EOL, wordwrap($text, $this->wrap, PHP_EOL)) : [$text];
        } else {
            $lines = [$text];
        }

        foreach ($lines as $line) {
            $this->response .= (($indent) ? $this->indent : '') . $line . (($newline) ? PHP_EOL : null);
        }

        return $this;
    }

    /**
     * Write a string of text to the response body and send the response
     *
     * @param  ?string $text
     * @param  bool    $newline
     * @param  bool    $indent
     * @param  bool    $withHeaders
     * @return Console
     */
    public function write(?string $text = null, bool $newline = true, bool $indent = true, bool $withHeaders = true): Console
    {
        $this->append($text, $newline, $indent);
        $this->send($withHeaders);
        return $this;
    }

    /**
     * Send the response
     *
     * @param  bool $withHeaders
     * @return Console
     */
    public function send(bool $withHeaders = true): Console
    {
        if ($withHeaders) {
            if (($this->header !== null) && !($this->headerSent)) {
                $this->response = $this->formatTemplate($this->header) . $this->response;
            }
            if ($this->footer !== null) {
                $this->response .= $this->formatTemplate($this->footer);
            }
        }

        echo $this->response;
        $this->response = null;
        return $this;
    }

    /**
     * Display console help
     *
     * @return void
     */
    public function displayHelp(): void
    {
        $this->response = null;
        $commands       = [];
        $commandLengths = [];

        if ($this->header !== null) {
            $this->response .= $this->formatTemplate($this->header);
        }

        foreach ($this->commands as $key => $command) {
            $name   = $command->getName();
            $params = $command->getParams();
            $length = strlen((string)$name);

            if (count($this->helpColors) > 0) {
                if (str_contains((string)$name, ' ')) {
                    $name1 = substr($name, 0, strpos($name, ' '));
                    $name2 = substr($name, strpos($name, ' ') + 1);
                    if (isset($this->helpColors[0])) {
                        $name1 = Color::colorize($name1, $this->helpColors[0]);
                    }
                    if (isset($this->helpColors[1])) {
                        $name2 = Color::colorize($name2, $this->helpColors[1]);
                    }
                    $name = $name1 . ' ' . $name2;
                } else if (isset($this->helpColors[0])){
                    $name = Color::colorize($name, $this->helpColors[0]);
                }
            }

            if ($params !== null) {
                $length += (strlen((string)$params) + 1);
                if (str_contains($params, '-') && str_contains($params, '<')) {
                    $pars = explode(' ', $params);
                    if (count($pars) > 0) {
                        $optionFirst = str_contains($pars[0], '-');
                        $colorIndex  = 2;
                        foreach ($pars as $p) {
                            if (isset($this->helpColors[3]) &&
                                (($optionFirst) && str_contains($p, '<')) || ((!$optionFirst) && str_contains($p, '-'))) {
                                $colorIndex = 3;
                            }
                            $name .= ' ' . ((isset($this->helpColors[$colorIndex])) ?
                                Color::colorize($p, $this->helpColors[$colorIndex]) : $p);
                        }
                    }
                } else {
                    $name .= ' ' . ((isset($this->helpColors[2])) ?
                        Color::colorize($params, $this->helpColors[2]) : $params);
                }
            }

            $commands[$key]       = $this->indent . $name;
            $commandLengths[$key] = $length;
        }

        $maxLength = max($commandLengths);
        $wrapped   = false;
        $i         = 0;

        foreach ($commands as $key => $command) {
            if ($this->commands[$key]->hasHelp()) {
                $help = $this->commands[$key]->getHelp();
                $pad  = ($commandLengths[$key] < $maxLength) ?
                    str_repeat(' ', $maxLength - $commandLengths[$key]) . '    ' : '    ';

                if (strlen((string)$this->commands[$key] . $pad . $help) > $this->wrap) {
                    if (!$wrapped) {
                        $this->response .= PHP_EOL;
                    }

                    $offset = $this->wrap - strlen((string)$this->commands[$key] . $pad);
                    $lines  = explode(PHP_EOL, wordwrap($help, $offset, PHP_EOL));
                    foreach ($lines as $i => $line) {
                        $this->response .= ($i == 0) ?
                            $command . $pad . $line . PHP_EOL :
                            $this->indent . str_repeat(' ', strlen((string)$this->commands[$key])) . $pad . $line . PHP_EOL;
                    }

                    if ($i < count($commands) - 1) {
                        $this->response .= PHP_EOL;
                    }
                    $wrapped = true;
                } else {
                    $this->response .= $command . $pad . $help . PHP_EOL;
                    $wrapped = false;
                }
            } else {
                $this->response .= $command . $this->commands[$key]->getHelp() . PHP_EOL;
            }
            $i++;
        }

        if ($this->footer !== null) {
            $this->response .= $this->formatTemplate($this->footer);
        }

        $this->send(false);
    }

    /**
     * Clear the console
     *
     * @return void
     */
    public function clear(): void
    {
        echo chr(27) . "[2J" . chr(27) . "[;H";
    }

    /**
     * Format header or footer template
     *
     * @param  string $template
     * @return string
     */
    protected function formatTemplate(string $template): string
    {
        $format = null;

        if (str_contains($template, "\n")) {
            $templateLines = explode("\n", $template);
            foreach ($templateLines as $line) {
                $line    = trim($line);
                $format .= $this->indent . $line . PHP_EOL;
            }
        } else {
            $format = $this->indent . $template . PHP_EOL;
        }

        return $format;
    }

    /**
     * Get prompt input
     *
     * @param  string $prompt
     * @param  int    $length
     * @param  bool   $caseSensitive
     * @return string
     */
    protected function getPromptInput(string $prompt, int $length = 500, bool $caseSensitive = false): string
    {
        if (isset($_SERVER['X_POP_CONSOLE_INPUT'])) {
            $input = ($caseSensitive) ?
                rtrim($_SERVER['X_POP_CONSOLE_INPUT']) : strtolower(rtrim($_SERVER['X_POP_CONSOLE_INPUT']));
        } else {
            $promptInput = fopen('php://stdin', 'r');
            $input       = fgets($promptInput, strlen((string)$prompt) + $length);
            $input       = ($caseSensitive) ? rtrim($input) : strtolower(rtrim($input));
            fclose($promptInput);
        }

        return $input;
    }

}
