<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp
 * @category   Pop
 * @package    Pop_Console
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2014 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Console;

/**
 * Console class
 *
 * @category   Pop
 * @package    Pop_Console
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2014 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    2.0.0a
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
     * Console request object
     * @var Request
     */
    protected $request = null;

    /**
     * Console response object
     * @var Response
     */
    protected $response = null;

    /**
     * Console character width
     * @var int
     */
    protected $width = 80;

    /**
     * Console arguments
     * @var array
     */
    protected $arguments = [];

    /**
     * Command objects
     * @var array
     */
    protected $commands = [];

    /**
     * Option objects
     * @var array
     */
    protected $options = [];

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
     * @param  array $commands
     * @param  array $options
     * @param  int   $width
     * @return Console
     */
    public function __construct(array $commands = null, array $options = null, $width = 80)
    {
        $this->request  = new Request();
        $this->response = new Response();
        if (null !== $commands) {
            $this->addCommands($commands);
        }
        if (null !== $options) {
            $this->addOptions($options);
        }
        $this->setWidth($width);
    }

    /**
     * Set the wrap width of the console object
     *
     * @param  int $width
     * @return string
     */
    public function setWidth($width)
    {
        $this->width = (int)$width;
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
     * Get the request object
     *
     * @return Request
     */
    public function request()
    {
        return $this->request;
    }

    /**
     * Get the response object
     *
     * @return Response
     */
    public function response()
    {
        return $this->response;
    }

    /**
     * Add a command
     *
     * @param  Input\Command $command
     * @return Console
     */
    public function addCommand(Input\Command $command)
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
     * Add an option
     *
     * @param  Input\Option $option
     * @return Console
     */
    public function addOption(Input\Option $option)
    {
        $name = ($option->hasLongName()) ? $option->getLongName() : $option->getShortName();
        $this->options[$name] = $option;
        return $this;
    }

    /**
     * Add options
     *
     * @param  array $options
     * @return Console
     */
    public function addOptions(array $options)
    {
        foreach ($options as $option) {
            $this->addOption($option);
        }
        return $this;
    }

    /**
     * Get arguments
     *
     * @return array
     */
    public function getArguments()
    {
        if (!$this->request->isParsed()) {
            $this->parseRequest();
        }
        return $this->arguments;
    }

    /**
     * Determine if an argument exists
     *
     * @param  string $arg
     * @return boolean
     */
    public function hasArgument($arg)
    {
        return isset($this->arguments[$arg]);
    }

    /**
     * Get a command value
     *
     * @param  string $command
     * @return mixed
     */
    public function getCommand($command)
    {
        if (!$this->request->isParsed()) {
            $this->parseRequest();
        }
        return (isset($this->commands[$command])) ? $this->commands[$command]->getValue() : null;
    }

    /**
     * Get commands
     *
     * @return array
     */
    public function getCommands()
    {
        if (!$this->request->isParsed()) {
            $this->parseRequest();
        }
        return $this->commands;
    }

    /**
     * Get an option value
     *
     * @param  string $option
     * @return mixed
     */
    public function getOption($option)
    {
        if (!$this->request->isParsed()) {
            $this->parseRequest();
        }
        return (isset($this->options[$option])) ? $this->options[$option]->getValue() : null;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function getOptions()
    {
        if (!$this->request->isParsed()) {
            $this->parseRequest();
        }
        return $this->options;
    }

    /**
     * Get required parameters that were not found
     *
     * @return array
     */
    public function getRequiredParamsNotFound()
    {
        if (!$this->request->isParsed()) {
            $this->parseRequest();
        }
        return $this->request->getRequiredParamsNotFound();
    }

    /**
     * Determine if a command exists
     *
     * @param  string $command
     * @return boolean
     */
    public function hasCommand($command)
    {
        return isset($this->commands[$command]);
    }

    /**
     * Determine if an option exists
     *
     * @param  string $option
     * @return boolean
     */
    public function hasOption($option)
    {
        return isset($this->options[$option]);
    }

    /**
     * Determine if the request is valid
     *
     * @return boolean
     */
    public function isRequestValid()
    {
        if (!$this->request->isParsed()) {
            $this->parseRequest();
        }
        return $this->request->isValid();
    }

    /**
     * Parse options
     *
     * @return void
     */
    public function parseRequest()
    {
        $this->request->parse($this->commands, $this->options);
        if ($this->request->isValid()) {
            $this->commands  = $this->request->getCommands();
            $this->options   = $this->request->getOptions();
            $this->arguments = $this->request->getArguments();
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
        $fgColor = $this->getColorCode($fg, 'foreground');
        $bgColor = $this->getColorCode($bg, 'background');
        return ($fgColor !== null ? "\x1b[" . $fgColor   . 'm' : '') .
        ($bgColor !== null ? "\x1b[" . $bgColor . 'm' : '') . $string . "\x1b[22;39m\x1b[0;49m";
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
     * Get input from the prompt
     *
     * @param  string  $prompt
     * @param  array   $options
     * @param  boolean $caseSensitive
     * @param  int     $length
     * @return string
     */
    public function prompt($prompt, array $options = null, $caseSensitive = false, $length = 500)
    {
        echo $prompt;
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
                    echo $prompt;
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
     * Write a string of text to the response body
     *
     * @param  string $text
     * @param  string $indent
     * @return Console
     */
    public function write($text, $indent = null)
    {
        $lines = (strlen($text) > $this->width) ?
            explode(PHP_EOL, wordwrap($text, $this->width, PHP_EOL)) : [$text];

        foreach ($lines as $line) {
            $this->response->append($indent . $line . PHP_EOL);
        }
        return $this;
    }

    /**
     * Send the response
     *
     * @return Console
     */
    public function send()
    {
        $this->response->send();
        return $this;
    }

    /**
     * Clear the console
     *
     * @return Console
     */
    public function clear()
    {
        echo chr(27) . "[2J" . chr(27) . "[;H";
    }

}
