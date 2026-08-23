<?php
declare(strict_types=1);
/**
 * Pop PHP Framework (https://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2026 Nick Sagona, III
 * @license    https://www.popphp.org/license     New BSD License
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
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2026 Nick Sagona, III
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.0
 */
class Console
{

    /**
     * Console wrap
     * @var ?int
     */
    protected ?int $wrap = null;

    /**
     * Console margin
     * @var ?int
     */
    protected ?int $margin = null;

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
     * Command registry
     * @var CommandRegistry
     */
    protected CommandRegistry $commands;

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
     * Custom input stream for prompt input (used in place of php://stdin when set)
     * @var mixed
     */
    protected mixed $inputStream = null;

    /**
     * Detected terminal size, cached per-process so the stty/tput subprocess
     * probe only ever runs once no matter how many Console instances are built
     * @var ?array
     */
    protected static ?array $detectedTerminalSize = null;

    /**
     * Instantiate a new console object
     *
     * @param  ?int            $wrap
     * @param  int|string|null $margin
     */
    public function __construct(?int $wrap = 80, int|string|null $margin = 4)
    {
        if (function_exists('exec') && stream_isatty(STDIN)) {
            if (self::$detectedTerminalSize === null) {
                self::$detectedTerminalSize = self::detectTerminalSize();
            }

            [$height, $width] = self::$detectedTerminalSize;
            if (!empty($height) && !empty($width)) {
                $this->setHeight((int)$height);
                $this->setWidth((int)$width);
            }
        }

        if ($wrap !== null) {
            $this->setWrap($wrap);
        }

        if (is_string($margin) && str_contains($margin, ' ')) {
            $this->setIndent($margin);
        } else if (is_numeric($margin)) {
            $this->setMargin((int)$margin);
        }

        $this->server   = $_SERVER;
        $this->env      = $_ENV;
        $this->commands = new CommandRegistry();
    }

    /**
     * Detect the terminal height/width via stty or tput
     *
     * @return array
     */
    protected static function detectTerminalSize(): array
    {
        $height = null;
        $width  = null;

        if (!empty(exec('which stty'))) {
            $sttySize = exec('stty size');
            if (!empty($sttySize) && str_contains($sttySize, ' ')) {
                [$height, $width] = explode(' ', $sttySize, 2);
            }
        } else if (!empty(exec('which tput'))) {
            $height = exec('tput lines');
            $width  = exec('tput cols');
        }

        return [$height, $width];
    }

    /**
     * Set the wrap width of the console object
     *
     * @param  int $wrap
     * @return Console
     */
    public function setWrap(int $wrap): Console
    {
        $this->wrap = $wrap;
        return $this;
    }

    /**
     * Set the margin of the console object
     *
     * @param  int $margin
     * @return Console
     */
    public function setMargin(int $margin): Console
    {
        $this->margin = $margin;
        return $this;
    }

    /**
     * Set the margin of the console object by way of an indentation string
     * (to maintain backwards compatibility)
     *
     * @param  string $indent
     * @return Console
     */
    public function setIndent(string $indent): Console
    {
        $this->margin = strlen($indent);
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
        $this->width = $width;
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
        $this->height = $height;
        return $this;
    }

    /**
     * Set the console header
     *
     * @param  string $header
     * @param  bool   $newline
     * @return Console
     */
    public function setHeader(string $header, bool $newline = true): Console
    {
        $this->header = $header;
        if ($newline) {
            $this->header .= PHP_EOL;
        }
        return $this;
    }

    /**
     * Set the console footer
     *
     * @param  string $footer
     * @param  bool   $newline
     * @return Console
     */
    public function setFooter(string $footer, bool $newline = true): Console
    {
        $this->footer = $footer;
        if ($newline) {
            $this->footer = PHP_EOL . $this->footer;
        }
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
        $this->headerSent = $headerSent;
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
     * Set the input stream for prompt input
     *
     * @param  mixed $stream
     * @throws Exception
     * @return Console
     */
    public function setInputStream(mixed $stream): Console
    {
        if (!is_resource($stream)) {
            throw new Exception('The input stream must be a valid resource.');
        }

        $this->inputStream = $stream;
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
     * Get the margin of the console object
     *
     * @return int
     */
    public function getMargin(): int
    {
        return $this->margin;
    }

    /**
     * Get the indent string based on the margin
     * (to maintain backwards compatibility)
     *
     * @return string
     */
    public function getIndent(): string
    {
        return str_repeat(' ', (int)$this->margin);
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
     * Check is console terminal supports color
     *
     * @return bool
     */
    public function isColor(): bool
    {
        return (isset($_SERVER['TERM']) && (stripos($_SERVER['TERM'], 'color') !== false));
    }

    /**
     * Check is console terminal is in a Windows environment
     *
     * @return bool
     */
    public function isWindows(): bool
    {
        return (stripos(PHP_OS, 'win') !== false);
    }

    /**
     * Has wrap
     *
     * @return bool
     */
    public function hasWrap(): bool
    {
        return !empty($this->wrap);
    }

    /**
     * Has margin
     *
     * @return bool
     */
    public function hasMargin(): bool
    {
        return !empty($this->margin);
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
     * Has input stream
     *
     * @return bool
     */
    public function hasInputStream(): bool
    {
        return ($this->inputStream !== null);
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
     * Get the input stream
     *
     * @return mixed
     */
    public function getInputStream(): mixed
    {
        return $this->inputStream;
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
        $this->commands->add($command);
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
        $this->commands->addAll($commands);
        return $this;
    }

    /**
     * Get commands
     *
     * @return array
     */
    public function getCommands(): array
    {
        return $this->commands->all();
    }

    /**
     * Get a command
     *
     * @param  string $command
     * @return Command|null
     */
    public function getCommand(string $command): Command|null
    {
        return $this->commands->get($command);
    }

    /**
     * Check if the console object has a command
     *
     * @param  string $command
     * @return bool
     */
    public function hasCommand(string $command): bool
    {
        return $this->commands->has($command);
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
        return $this->commands->fromRoutes($routeMatch, $scriptName);
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
        $this->commands->addFromRoutes($routeMatch, $scriptName);
        return $this;
    }

    /**
     * Get a help
     *
     * @param  ?string $command
     * @param  bool    $raw
     * @param  ?string $subCommand
     * @return string|null
     */
    public function help(?string $command = null, bool $raw = false, ?string $subCommand = null): string|null
    {
        if ($command !== null) {
            return $this->commands->get($command)?->getHelp();
        } else {
            $this->displayHelp($raw, $subCommand);
            return null;
        }
    }

    /**
     * Print a horizontal line rule out to the console
     *
     * @param  string $char
     * @param  ?int   $size
     * @param  bool   $newline
     * @param  bool   $return
     * @return Console|string
     */
    public function line(string $char = '-', ?int $size = null, bool $newline = true, bool $return = false): Console|string
    {
        $result = (new Header($this->getIndent(), $this->wrap, $this->width, $this->margin))->line($char, $size, $newline);

        if ($return) {
            return $result;
        }

        echo $result;
        return $this;
    }

    /**
     * Print a header
     *
     * @param  string          $string
     * @param  string          $char
     * @param  int|string|null $size
     * @param  string          $align
     * @param  bool            $newline
     * @param  bool            $return
     * @return Console|string
     */
    public function header(
        string $string, string $char = '-', int|string|null $size = null,
        string $align = 'left', bool $newline = true, bool $return = false
    ): Console|string
    {
        $result = (new Header($this->getIndent(), $this->wrap, $this->width, $this->margin))
            ->header($string, $char, $size, $align, $newline);

        if ($return) {
            return $result;
        }

        echo $result;
        return $this;
    }

    /**
     * Print a left header
     *
     * @param  string          $string
     * @param  string          $char
     * @param  int|string|null $size
     * @param  bool            $newline
     * @param  bool            $return
     * @return Console|string
     */
    public function headerLeft(
        string $string, string $char = '-', int|string|null $size = 'auto', bool $newline = true, bool $return = false
    ): Console|string
    {
        $result = (new Header($this->getIndent(), $this->wrap, $this->width, $this->margin))
            ->headerLeft($string, $char, $size, $newline);

        if ($return) {
            return $result;
        }

        echo $result;
        return $this;
    }

    /**
     * Print a center header
     *
     * @param  string          $string
     * @param  string          $char
     * @param  int|string|null $size
     * @param  bool            $newline
     * @param  bool            $return
     * @return Console|string
     */
    public function headerCenter(
        string $string, string $char = '-', int|string|null $size = 'auto', bool $newline = true, bool $return = false
    ): Console|string
    {
        $result = (new Header($this->getIndent(), $this->wrap, $this->width, $this->margin))
            ->headerCenter($string, $char, $size, $newline);

        if ($return) {
            return $result;
        }

        echo $result;
        return $this;
    }

    /**
     * Print a right header
     *
     * @param  string          $string
     * @param  string          $char
     * @param  int|string|null $size
     * @param  bool            $newline
     * @param  bool            $return
     * @return Console|string
     */
    public function headerRight(
        string $string, string $char = '-', int|string|null $size = 'auto', bool $newline = true, bool $return = false
    ): Console|string
    {
        $result = (new Header($this->getIndent(), $this->wrap, $this->width, $this->margin))
            ->headerRight($string, $char, $size, $newline);

        if ($return) {
            return $result;
        }

        echo $result;
        return $this;
    }

    /**
     * Print a colored alert box out to the console
     *
     * @param  string          $message
     * @param  int             $fg
     * @param  int             $bg
     * @param  int|string|null $size
     * @param  string          $align
     * @param  int             $innerPad
     * @param  bool            $newline
     * @param  bool            $return
     * @return Console|string
     */
    public function alert(
        string $message, int $fg, int $bg, int|string|null $size = null, string $align = 'center',
        int $innerPad = 4, bool $newline = true, bool $return = false
    ): Console|string
    {
        $result = (new Alert($this->getIndent(), $this->wrap, $this->width, $this->margin))
            ->alert($message, $fg, $bg, $size, $align, $innerPad, $newline);

        if ($return) {
            return $result;
        }

        echo $result;
        return $this;
    }

    /**
     * Print a colorless alert outline box out to the console
     *
     * @param  string          $message
     * @param  string          $h
     * @param  ?string         $v
     * @param  int|string|null $size
     * @param  string          $align
     * @param  int             $innerPad
     * @param  bool            $newline
     * @param  bool            $return
     * @return Console|string
     */
    public function alertBox(
        string $message, string $h = '-', ?string $v = '|', int|string|null $size = null,
        string $align = 'center', int $innerPad = 4, bool $newline = true, bool $return = false
    ): Console|string
    {
        $result = (new Alert($this->getIndent(), $this->wrap, $this->width, $this->margin))
            ->alertBox($message, $h, $v, $size, $align, $innerPad, $newline);

        if ($return) {
            return $result;
        }

        echo $result;
        return $this;
    }

    /**
     * Print a "danger" alert box out to the console
     *
     * @param  string          $message
     * @param  int|string|null $size
     * @param  string          $align
     * @param  int             $innerPad
     * @param  bool            $newline
     * @param  bool            $return
     * @return Console|string
     */
    public function alertDanger(
        string $message, int|string|null $size = null, string $align = 'center',
        int $innerPad = 4, bool $newline = true, bool $return = false
    ): Console|string
    {
        $result = (new Alert($this->getIndent(), $this->wrap, $this->width, $this->margin))
            ->alertDanger($message, $size, $align, $innerPad, $newline);

        if ($return) {
            return $result;
        }

        echo $result;
        return $this;
    }

    /**
     * Print a "warning" alert box out to the console
     *
     * @param  string          $message
     * @param  int|string|null $size
     * @param  string          $align
     * @param  int             $innerPad
     * @param  bool            $newline
     * @param  bool            $return
     * @return Console|string
     */
    public function alertWarning(
        string $message, int|string|null $size = null, string $align = 'center',
        int $innerPad = 4, bool $newline = true, bool $return = false
    ): Console|string
    {
        $result = (new Alert($this->getIndent(), $this->wrap, $this->width, $this->margin))
            ->alertWarning($message, $size, $align, $innerPad, $newline);

        if ($return) {
            return $result;
        }

        echo $result;
        return $this;
    }

    /**
     * Print a "success" alert box out to the console
     *
     * @param  string          $message
     * @param  int|string|null $size
     * @param  string          $align
     * @param  int             $innerPad
     * @param  bool            $newline
     * @param  bool            $return
     * @return Console|string
     */
    public function alertSuccess(
        string $message, int|string|null $size = null, string $align = 'center',
        int $innerPad = 4, bool $newline = true, bool $return = false
    ): Console|string
    {
        $result = (new Alert($this->getIndent(), $this->wrap, $this->width, $this->margin))
            ->alertSuccess($message, $size, $align, $innerPad, $newline);

        if ($return) {
            return $result;
        }

        echo $result;
        return $this;
    }

    /**
     * Print an "info" alert box out to the console
     *
     * @param  string          $message
     * @param  int|string|null $size
     * @param  string          $align
     * @param  int             $innerPad
     * @param  bool            $newline
     * @param  bool            $return
     * @return Console|string
     */
    public function alertInfo(
        string $message, int|string|null $size = null, string $align = 'center',
        int $innerPad = 4, bool $newline = true, bool $return = false
    ): Console|string
    {
        $result = (new Alert($this->getIndent(), $this->wrap, $this->width, $this->margin))
            ->alertInfo($message, $size, $align, $innerPad, $newline);

        if ($return) {
            return $result;
        }

        echo $result;
        return $this;
    }

    /**
     * Print a "primary" alert box out to the console
     *
     * @param  string          $message
     * @param  int|string|null $size
     * @param  string          $align
     * @param  int             $innerPad
     * @param  bool            $newline
     * @param  bool            $return
     * @return Console|string
     */
    public function alertPrimary(
        string $message, int|string|null $size = null, string $align = 'center',
        int $innerPad = 4, bool $newline = true, bool $return = false
    ): Console|string
    {
        $result = (new Alert($this->getIndent(), $this->wrap, $this->width, $this->margin))
            ->alertPrimary($message, $size, $align, $innerPad, $newline);

        if ($return) {
            return $result;
        }

        echo $result;
        return $this;
    }

    /**
     * Print a "secondary" alert box out to the console
     *
     * @param  string          $message
     * @param  int|string|null $size
     * @param  string          $align
     * @param  int             $innerPad
     * @param  bool            $newline
     * @param  bool            $return
     * @return Console|string
     */
    public function alertSecondary(
        string $message, int|string|null $size = null, string $align = 'center',
        int $innerPad = 4, bool $newline = true, bool $return = false
    ): Console|string
    {
        $result = (new Alert($this->getIndent(), $this->wrap, $this->width, $this->margin))
            ->alertSecondary($message, $size, $align, $innerPad, $newline);

        if ($return) {
            return $result;
        }

        echo $result;
        return $this;
    }

    /**
     * Print a "dark" alert box out to the console
     *
     * @param  string          $message
     * @param  int|string|null $size
     * @param  string          $align
     * @param  int             $innerPad
     * @param  bool            $newline
     * @param  bool            $return
     * @return Console|string
     */
    public function alertDark(
        string $message, int|string|null $size = null, string $align = 'center',
        int $innerPad = 4, bool $newline = true, bool $return = false
    ): Console|string
    {
        $result = (new Alert($this->getIndent(), $this->wrap, $this->width, $this->margin))
            ->alertDark($message, $size, $align, $innerPad, $newline);

        if ($return) {
            return $result;
        }

        echo $result;
        return $this;
    }

    /**
     * Print a "light" alert box out to the console
     *
     * @param  string          $message
     * @param  int|string|null $size
     * @param  string          $align
     * @param  int             $innerPad
     * @param  bool            $newline
     * @param  bool            $return
     * @return Console|string
     */
    public function alertLight(
        string $message, int|string|null $size = null, string $align = 'center',
        int $innerPad = 4, bool $newline = true, bool $return = false
    ): Console|string
    {
        $result = (new Alert($this->getIndent(), $this->wrap, $this->width, $this->margin))
            ->alertLight($message, $size, $align, $innerPad, $newline);

        if ($return) {
            return $result;
        }

        echo $result;
        return $this;
    }

    /**
     * Print a table out to the console
     *
     * @param  array   $headers
     * @param  array   $rows
     * @param  string  $h
     * @param  ?string $v
     * @param  ?int    $headerFg
     * @param  ?int    $headerBg
     * @param  bool    $newline
     * @param  bool    $return
     * @return Console|string
     */
    public function table(
        array $headers, array $rows, string $h = '-', ?string $v = '|',
        ?int $headerFg = null, ?int $headerBg = null, bool $newline = true, bool $return = false
    ): Console|string
    {
        $table = new Table($headers, $rows, $h, $v);
        if (($headerFg !== null) || ($headerBg !== null)) {
            $table->setHeaderColor($headerFg, $headerBg);
        }

        $output = '';
        foreach (explode(PHP_EOL, rtrim($table->render(), PHP_EOL)) as $line) {
            $output .= $this->getIndent() . $line . PHP_EOL;
        }
        if ($newline) {
            $output .= PHP_EOL;
        }

        if ($return) {
            return $output;
        } else {
            echo $output;
            return $this;
        }
    }

    /**
     * Create a progress bar
     *
     * @param  int     $total
     * @param  ?string $message
     * @param  int     $width
     * @return ProgressBar
     */
    public function progressBar(int $total, ?string $message = null, int $width = 28): ProgressBar
    {
        $bar = new ProgressBar($total, $message, $width);
        $bar->setIndent($this->getIndent());
        return $bar;
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
        $formattedHeader = null;
        if (($withHeaders) && ($this->header !== null)) {
            $this->headerSent = true;
            $formattedHeader = $this->formatTemplate($this->header);
        }

        return (new Prompt($this->getIndent(), $formattedHeader, $this->inputStream))
            ->prompt($prompt, $options, $caseSensitive, $length);
    }

    /**
     * Display a prompt that accepts one or more comma-separated selections
     *
     * @param  string $prompt
     * @param  array  $options
     * @param  bool   $caseSensitive
     * @param  int    $length
     * @param  bool   $withHeaders
     * @return array
     */
    public function promptMulti(
        string $prompt, array $options, bool $caseSensitive = false, int $length = 500, bool $withHeaders = true
    ): array
    {
        $formattedHeader = null;
        if (($withHeaders) && ($this->header !== null)) {
            $this->headerSent = true;
            $formattedHeader = $this->formatTemplate($this->header);
        }

        return (new Prompt($this->getIndent(), $formattedHeader, $this->inputStream))
            ->promptMulti($prompt, $options, $caseSensitive, $length);
    }

    /**
     * Display confirm message prompt
     *
     * @param  string $message
     * @param  array  $options
     * @param  bool   $caseSensitive
     * @param  int    $length
     * @param  bool   $withHeaders
     * @param  bool   $exit
     * @return string
     */
    public function confirm(
        string $message = 'Are you sure?', array $options = ['Y', 'N'], bool $caseSensitive = false,
        int $length = 500, bool $withHeaders = true, bool $exit = true
    ): string
    {
        $message .= ' [' . implode('/', $options) . '] ';
        $response = $this->prompt($message, $options, $caseSensitive, $length, $withHeaders);

        if (($exit) && ((strtolower($response) == 'n') || (strtolower($response) == 'no'))) {
            echo PHP_EOL;
            exit(127);
        }

        return $response;
    }

    /**
     * Colorize a string for output
     *
     * @param  string $string
     * @param  ?int   $fg
     * @param  ?int   $bg
     * @return string
     */
    public function colorize(string $string, ?int $fg = null, ?int $bg = null): string
    {
        return Color::colorize($string, $fg, $bg);
    }

    /**
     * Append a string of text to the response body
     *
     * @param  ?string $text
     * @param  bool    $newline
     * @param  bool    $margin
     * @return Console
     */
    public function append(?string $text = null, bool $newline = true, bool $margin = true): Console
    {
        if (!empty($this->wrap)) {
            $lines = (strlen((string)$text) > $this->wrap) ?
                explode(PHP_EOL, wordwrap($text, $this->wrap, PHP_EOL)) : [$text];
        } else if (!empty($this->width)) {
            $lines = (strlen((string)$text) > ($this->width - ((int)$this->margin * 2))) ?
                explode(PHP_EOL, wordwrap($text, ($this->width - ((int)$this->margin * 2)), PHP_EOL)) : [$text];
        } else {
            $lines = [$text];
        }

        foreach ($lines as $line) {
            $this->response .= (($margin) ? $this->getIndent() : '') . $line . (($newline) ? PHP_EOL : null);
        }

        return $this;
    }

    /**
     * Write a string of text to the response body and send the response
     *
     * @param  ?string $text
     * @param  bool    $newline
     * @param  bool    $margin
     * @param  bool    $withHeaders
     * @return Console
     */
    public function write(?string $text = null, bool $newline = true, bool $margin = true, bool $withHeaders = true): Console
    {
        $this->append($text, $newline, $margin);
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
     * @param  bool    $raw
     * @param  ?string $subCommand
     * @return void
     */
    public function displayHelp(bool $raw = false, ?string $subCommand = null): void
    {
        $this->response = null;

        if ($this->header !== null) {
            $this->response .= $this->formatTemplate($this->header);
        }

        $this->response .= (new Help())->render($this->commands->all(), $raw, $subCommand, $this->getIndent(), $this->wrap, $this->helpColors);

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
                $format .= $this->getIndent() . $line . PHP_EOL;
            }
        } else {
            $format = $this->getIndent() . $template . PHP_EOL;
        }

        return $format;
    }

}
