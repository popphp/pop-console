<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2017 NOLA Interactive, LLC. (http://www.nolainteractive.com)
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
 * @package    Pop\Console
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2017 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.0.0
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
    public function __construct($width = 80, $indent = null)
    {
        $this->setWidth($width);
        $this->setIndent($indent);
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
     * Add a command
     *
     * @param  Command $command
     * @return Console
     */
    public function addCommand(Command $command)
    {
        $this->commands[(string)$command] = $command;
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
     * Get a command help
     *
     * @param  string $command
     * @return string
     */
    public function help($command)
    {
        return (isset($this->commands[$command])) ? $this->commands[$command]->getHelp() : null;
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
     * @return string
     */
    public function prompt($prompt, array $options = null, $caseSensitive = false, $length = 500)
    {
        echo $this->indent . $prompt;
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
     * @return Console
     */
    public function write($text = null, $newline = true)
    {
        $this->append($text, $newline);
        $this->send();
        return $this;
    }

    /**
     * Send the response
     *
     * @return Console
     */
    public function send()
    {
        echo $this->response;
        $this->response = null;
        return $this;
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

}
