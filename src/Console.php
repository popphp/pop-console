<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2019 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Console;

use Pop\Router\Match;

/**
 * Console class
 *
 * @category   Pop
 * @package    Pop\Console
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2019 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.1.0
 */
class Console
{

    /**
     * Color indices
     */
    const NORMAL        = 0;
    const BLACK         = 1;
    const RED           = 2;
    const GREEN         = 3;
    const YELLOW        = 4;
    const BLUE          = 5;
    const MAGENTA       = 6;
    const CYAN          = 7;
    const WHITE         = 8;
    const GRAY          = 9;
    const BOLD_RED     = 10;
    const BOLD_GREEN   = 11;
    const BOLD_YELLOW  = 12;
    const BOLD_BLUE    = 13;
    const BOLD_MAGENTA = 14;
    const BOLD_CYAN    = 15;
    const BOLD_WHITE   = 16;

    /**
     * Console character width
     * @var int
     */
    protected $width = 80;

    /**
     * Console indentation
     * @var string
     */
    protected $indent = null;

    /**
     * Console response body
     * @var string
     */
    protected $response = null;

    /**
     * Commands
     * @var array
     */
    protected $commands = [];

    /**
     * Console header
     * @var string
     */
    protected $header = null;

    /**
     * Flag for if console header has been sent
     * @var boolean
     */
    protected $headerSent = false;

    /**
     * Console footer
     * @var string
     */
    protected $footer = null;

    /**
     * Help colors
     * @var array
     */
    protected $helpColors = [];

    /**
     * SERVER array
     * @var array
     */
    protected $server = [];

    /**
     * ENV array
     * @var array
     */
    protected $env    = [];

    /**
     * Color map of ansi values
     *
     * @var array
     */
    protected static $colorMap = [
        'foreground' => [
            self::NORMAL       => '22;39',
            self::BLACK        => '0;30',
            self::RED          => '0;31',
            self::GREEN        => '0;32',
            self::YELLOW       => '0;33',
            self::BLUE         => '0;34',
            self::MAGENTA      => '0;35',
            self::CYAN         => '0;36',
            self::WHITE        => '0;37',
            self::GRAY         => '1;30',
            self::BOLD_RED     => '1;31',
            self::BOLD_GREEN   => '1;32',
            self::BOLD_YELLOW  => '1;33',
            self::BOLD_BLUE    => '1;34',
            self::BOLD_MAGENTA => '1;35',
            self::BOLD_CYAN    => '1;36',
            self::BOLD_WHITE   => '1;37'
        ],
        'background' => [
            self::NORMAL       => '0;49',
            self::BLACK        => '40',
            self::RED          => '41',
            self::GREEN        => '42',
            self::YELLOW       => '43',
            self::BLUE         => '44',
            self::MAGENTA      => '45',
            self::CYAN         => '46',
            self::WHITE        => '47'
        ]
    ];

    /**
     * Instantiate a new console object
     *
     * @param  int    $width
     * @param  string $indent
     */
    public function __construct($width = 80, $indent = '    ')
    {
        $this->setWidth($width);
        $this->setIndent($indent);

        $this->server = (isset($_SERVER)) ? $_SERVER : [];
        $this->env    = (isset($_ENV))    ? $_ENV    : [];
    }

    /**
     * Set the wrap width of the console object
     *
     * @param  int $width
     * @return Console
     */
    public function setWidth($width)
    {
        $this->width = (int)$width;
        return $this;
    }

    /**
     * Set the indentation of the console object
     *
     * @param  string $indent
     * @return Console
     */
    public function setIndent($indent = null)
    {
        $this->indent = $indent;
        return $this;
    }

    /**
     * Set the console header
     *
     * @param  string $header
     * @return Console
     */
    public function setHeader($header)
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
    public function setFooter($footer)
    {
        $this->footer = $footer;
        return $this;
    }

    /**
     * Set the console header sent flag
     *
     * @param  boolean $headerSent
     * @return Console
     */
    public function setHeaderSent($headerSent = true)
    {
        $this->headerSent = (bool)$headerSent;
        return $this;
    }

    /**
     * Set the console help colors
     *
     * @param  int $color1
     * @param  int $color2
     * @param  int $color3
     * @return Console
     */
    public function setHelpColors($color1, $color2 = null, $color3 = null)
    {
        $this->helpColors = [
            $color1
        ];
        if (null !== $color2) {
            $this->helpColors[] = $color2;
        }
        if (null !== $color3) {
            $this->helpColors[] = $color3;
        }

        return $this;
    }

    /**
     * Get the wrap width of the console object
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Get the indentation of the console object
     *
     * @return string
     */
    public function getIndent()
    {
        return $this->indent;
    }

    /**
     * Get the console header
     *
     * @param  boolean $formatted
     * @return string
     */
    public function getHeader($formatted = false)
    {
        return ($formatted) ? $this->formatTemplate($this->header) : $this->header;
    }

    /**
     * Get the console footer
     *
     * @param  boolean $formatted
     * @return string
     */
    public function getFooter($formatted = false)
    {
        return ($formatted) ? $this->formatTemplate($this->footer) : $this->footer;
    }

    /**
     * Get the console header sent flag
     *
     * @return boolean
     */
    public function getHeaderSent()
    {
        return $this->headerSent;
    }

    /**
     * Get the console help colors
     *
     * @return array
     */
    public function getHelpColors()
    {
        return $this->helpColors;
    }

    /**
     * Get a value from $_SERVER, or the whole array
     *
     * @param  string $key
     * @return string|array
     */
    public function getServer($key = null)
    {
        if (null === $key) {
            return $this->server;
        } else {
            return (isset($this->server[$key])) ? $this->server[$key] : null;
        }
    }

    /**
     * Get a value from $_ENV, or the whole array
     *
     * @param  string $key
     * @return string|array
     */
    public function getEnv($key = null)
    {
        if (null === $key) {
            return $this->env;
        } else {
            return (isset($this->env[$key])) ? $this->env[$key] : null;
        }
    }

    /**
     * Add a command
     *
     * @param  Command $command
     * @return Console
     */
    public function addCommand(Command $command)
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
    public function addCommands(array $commands)
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
    public function getCommands()
    {
        return $this->commands;
    }

    /**
     * Get a command
     *
     * @param  string $command
     * @return Command
     */
    public function getCommand($command)
    {
        return (isset($this->commands[$command])) ? $this->commands[$command] : null;
    }

    /**
     * Check if the console object has a command
     *
     * @param  string $command
     * @return boolean
     */
    public function hasCommand($command)
    {
        return isset($this->commands[$command]);
    }

    /**
     * Get commands from routes
     *
     * @param  Match\Cli $routeMatch
     * @param  string    $scriptName
     * @return array
     */
    public function getCommandsFromRoutes(Match\Cli $routeMatch, $scriptName = null)
    {
        $routeMatch->match();

        $commandRoutes = $routeMatch->getRoutes();
        $commands      = $routeMatch->getCommands();
        $commandsToAdd = [];

        foreach ($commands as $name => $command) {
            $commandName = implode(' ', $command);
            $params      = trim(substr($name, strlen($commandName)));
            $params      = (!empty($params)) ? $params : null;
            $help        = (isset($commandRoutes[$name]) && isset($commandRoutes[$name]['help'])) ?
                $commandRoutes[$name]['help'] : null;

            if (null !== $scriptName) {
                $commandName = $scriptName . ' ' . $commandName;
            }

            $commandsToAdd[] = new Command($commandName, $params, $help);
        }

        return $commandsToAdd;
    }

    /**
     * Add commands from routes
     *
     * @param  Match\Cli $routeMatch
     * @param  string    $scriptName
     * @return Console
     */
    public function addCommandsFromRoutes(Match\Cli $routeMatch, $scriptName = null)
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
     * @param  string $command
     * @return string
     */
    public function help($command = null)
    {
        if (null !== $command) {
            return (isset($this->commands[$command])) ? $this->commands[$command]->getHelp() : null;
        } else {
            $this->displayHelp();
        }
    }

    /**
     * Colorize a string for output
     *
     * @param  string $string
     * @param  int    $fg
     * @param  int    $bg
     * @return string
     */
    public function colorize($string, $fg = null, $bg = null)
    {
        if (stripos(PHP_OS, 'win') === false) {
            $fgColor = $this->getColorCode($fg, 'foreground');
            $bgColor = $this->getColorCode($bg, 'background');
            return ($fgColor !== null ? "\x1b[" . $fgColor . 'm' : '') .
                ($bgColor !== null ? "\x1b[" . $bgColor . 'm' : '') . $string . "\x1b[0m";
        } else {
            return $string;
        }
    }

    /**
     * Get input from the prompt
     *
     * @param  string  $prompt
     * @param  array   $options
     * @param  boolean $caseSensitive
     * @param  int     $length
     * @param  boolean $withHeaders
     * @return string
     */
    public function prompt($prompt, array $options = null, $caseSensitive = false, $length = 500, $withHeaders = true)
    {
        if (($withHeaders) && (null !== $this->header)) {
            $this->headerSent = true;
            echo $this->formatTemplate($this->header) . $this->indent . $prompt;
        } else {
            echo $this->indent . $prompt;
        }

        $input = null;

        if (null !== $options) {
            $length = 0;
            foreach ($options as $key => $value) {
                $options[$key] = ($caseSensitive) ? $value : strtolower($value);
                if (strlen($value) > $length) {
                    $length = strlen($value);
                }
            }

            while (!in_array($input, $options)) {
                if (null !== $input) {
                    echo $this->indent . $prompt;
                }
                $promptInput = fopen('php://stdin', 'r');
                $input       = fgets($promptInput, strlen($prompt) . $length);
                $input       = ($caseSensitive) ? rtrim($input) : strtolower(rtrim($input));
                fclose($promptInput);
            }
        } else {
            while (null === $input) {
                $promptInput = fopen('php://stdin', 'r');
                $input       = fgets($promptInput, strlen($prompt) + $length);
                $input       = ($caseSensitive) ? rtrim($input) : strtolower(rtrim($input));
                fclose($promptInput);
            }
        }

        return $input;
    }

    /**
     * Append a string of text to the response body
     *
     * @param  string  $text
     * @param  boolean $newline
     * @return Console
     */
    public function append($text = null, $newline = true)
    {
        if ($this->width != 0) {
            $lines = (strlen($text) > $this->width) ?
                explode(PHP_EOL, wordwrap($text, $this->width, PHP_EOL)) : [$text];
        } else {
            $lines = [$text];
        }

        foreach ($lines as $line) {
            $this->response .=  $this->indent . $line . (($newline) ? PHP_EOL : null);
        }
        return $this;
    }

    /**
     * Write a string of text to the response body and send the response
     *
     * @param  string $text
     * @param  boolean $newline
     * @param  boolean $withHeaders
     * @return Console
     */
    public function write($text = null, $newline = true, $withHeaders = true)
    {
        $this->append($text, $newline);
        $this->send($withHeaders);
        return $this;
    }

    /**
     * Send the response
     *
     * @param  boolean $withHeaders
     * @return Console
     */
    public function send($withHeaders = true)
    {
        if ($withHeaders) {
            if ((null !== $this->header) && !($this->headerSent)) {
                $this->response = $this->formatTemplate($this->header) . $this->response;
            }
            if (null !== $this->footer) {
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
    public function displayHelp()
    {
        $this->response = null;
        $commands       = [];
        $commandLengths = [];

        if (null !== $this->header) {
            $this->response .= $this->formatTemplate($this->header);
        }

        foreach ($this->commands as $key => $command) {
            $name   = $command->getName();
            $params = $command->getParams();
            $length = strlen($name);

            if (count($this->helpColors) > 0) {
                if (strpos($name, ' ') !== false) {
                    $name1 = substr($name, 0, strpos($name, ' '));
                    $name2 = substr($name, strpos($name, ' ') + 1);
                    if (isset($this->helpColors[0])) {
                        $name1 = $this->colorize($name1, $this->helpColors[0]);
                    }
                    if (isset($this->helpColors[1])) {
                        $name2 = $this->colorize($name2, $this->helpColors[1]);
                    }
                    $name = $name1 . ' ' . $name2;
                } else if (isset($this->helpColors[0])){
                    $name = $this->colorize($name, $this->helpColors[0]);
                }
            }

            if (null !== $params) {
                $length += (strlen($params) + 1);
                $name   .= ' ' . ((isset($this->helpColors[2])) ? $this->colorize($params, $this->helpColors[2]) : $params);
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

                if (strlen((string)$this->commands[$key] . $pad . $help) > $this->width) {
                    if (!$wrapped) {
                        $this->response .= PHP_EOL;
                    }

                    $offset = $this->width - strlen((string)$this->commands[$key] . $pad);
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

        if (null !== $this->footer) {
            $this->response .= $this->formatTemplate($this->footer);
        }

        $this->send(false);
    }

    /**
     * Clear the console
     *
     * @return void
     */
    public function clear()
    {
        echo chr(27) . "[2J" . chr(27) . "[;H";
    }

    /**
     * Get the color code from the color map
     *
     * @param  int    $color
     * @param  string $type
     * @return mixed
     */
    protected function getColorCode($color, $type = 'foreground')
    {
        if (isset(static::$colorMap[$type]) && isset(static::$colorMap[$type][$color])) {
            return static::$colorMap[$type][$color];
        }
        return null;
    }

    /**
     * Format header or footer template
     *
     * @param  string $template
     * @return string
     */
    protected function formatTemplate($template)
    {
        $format = null;

        if (strpos($template, "\n") !== false) {
            $templateLines = explode("\n", $template);
            foreach ($templateLines as $line) {
                $line = trim($line);
                $format .= $this->indent . $line . PHP_EOL;
            }
        } else {
            $format = $this->indent . $template . PHP_EOL;
        }

        return $format;
    }

}
