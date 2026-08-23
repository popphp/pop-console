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

/**
 * Console alert class
 *
 * @category   Pop
 * @package    Pop\Console
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2026 Nick Sagona, III
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.0
 */
class Alert
{

    use MessageTrait;

    /**
     * Instantiate the alert object
     *
     * @param string $indent
     * @param ?int   $wrap
     * @param int    $width
     * @param ?int   $margin
     */
    public function __construct(
        protected string $indent = '', protected ?int $wrap = null, protected int $width = 0, protected ?int $margin = null
    ) { }

    /**
     * Build a colored alert box
     *
     * @param  string          $message
     * @param  int             $fg
     * @param  int             $bg
     * @param  int|string|null $size
     * @param  string          $align
     * @param  int             $innerPad
     * @param  bool            $newline
     * @return string
     */
    public function alert(
        string $message, int $fg, int $bg, int|string|null $size = null, string $align = 'center',
        int $innerPad = 4, bool $newline = true
    ): string
    {
        $size         = $this->resolveSize($message, $size, $this->wrap, $this->width, $this->margin, $innerPad * 2);
        $messageLines = $this->buildAlignedMessageLines($message, $size, $align, $innerPad);

        $alert = $this->indent . Color::colorize(str_repeat(' ', $size), $fg, $bg) . PHP_EOL;
        foreach ($messageLines as $messageLine) {
            $alert .= $this->indent . Color::colorize($messageLine, $fg, $bg) . PHP_EOL;
        }
        $alert .= $this->indent . Color::colorize(str_repeat(' ', $size), $fg, $bg) . PHP_EOL;
        if ($newline) {
            $alert .= PHP_EOL;
        }

        return $alert;
    }

    /**
     * Build a colorless alert outline box
     *
     * @param  string          $message
     * @param  string          $h
     * @param  ?string         $v
     * @param  int|string|null $size
     * @param  string          $align
     * @param  int             $innerPad
     * @param  bool            $newline
     * @return string
     */
    public function alertBox(
        string $message, string $h = '-', ?string $v = '|', int|string|null $size = null,
        string $align = 'center', int $innerPad = 4, bool $newline = true
    ): string
    {
        $size         = $this->resolveSize($message, $size, $this->wrap, $this->width, $this->margin, $innerPad * 2);
        $messageLines = $this->buildAlignedMessageLines($message, $size, $align, $innerPad);

        $alert  = $this->indent . str_repeat($h, $size) . PHP_EOL;
        $alert .= $this->indent . $v . str_repeat(' ', $size - 2) . $v . PHP_EOL;
        foreach ($messageLines as $messageLine) {
            if (!empty($v) && str_starts_with($messageLine, ' ') && str_ends_with($messageLine, ' ')) {
                $messageLine = $v . substr($messageLine, 1, -1) . $v;
            }
            $alert .= $this->indent . $messageLine . PHP_EOL;
        }
        $alert .= $this->indent . $v . str_repeat(' ', $size - 2) . $v . PHP_EOL;
        $alert .= $this->indent . str_repeat($h, $size) . PHP_EOL;
        if ($newline) {
            $alert .= PHP_EOL;
        }

        return $alert;
    }

    /**
     * Build a "danger" alert box
     *
     * @param  string          $message
     * @param  int|string|null $size
     * @param  string          $align
     * @param  int             $innerPad
     * @param  bool            $newline
     * @return string
     */
    public function alertDanger(string $message, int|string|null $size = null, string $align = 'center', int $innerPad = 4, bool $newline = true): string
    {
        return $this->alert($message, Color::BRIGHT_BOLD_WHITE, Color::BRIGHT_RED, $size, $align, $innerPad, $newline);
    }

    /**
     * Build a "warning" alert box
     *
     * @param  string          $message
     * @param  int|string|null $size
     * @param  string          $align
     * @param  int             $innerPad
     * @param  bool            $newline
     * @return string
     */
    public function alertWarning(string $message, int|string|null $size = null, string $align = 'center', int $innerPad = 4, bool $newline = true): string
    {
        return $this->alert($message, Color::BOLD_BLACK, Color::BRIGHT_YELLOW, $size, $align, $innerPad, $newline);
    }

    /**
     * Build a "success" alert box
     *
     * @param  string          $message
     * @param  int|string|null $size
     * @param  string          $align
     * @param  int             $innerPad
     * @param  bool            $newline
     * @return string
     */
    public function alertSuccess(string $message, int|string|null $size = null, string $align = 'center', int $innerPad = 4, bool $newline = true): string
    {
        return $this->alert($message, Color::BOLD_BLACK, Color::GREEN, $size, $align, $innerPad, $newline);
    }

    /**
     * Build an "info" alert box
     *
     * @param  string          $message
     * @param  int|string|null $size
     * @param  string          $align
     * @param  int             $innerPad
     * @param  bool            $newline
     * @return string
     */
    public function alertInfo(string $message, int|string|null $size = null, string $align = 'center', int $innerPad = 4, bool $newline = true): string
    {
        return $this->alert($message, Color::BRIGHT_BOLD_WHITE, Color::BRIGHT_BLUE, $size, $align, $innerPad, $newline);
    }

    /**
     * Build a "primary" alert box
     *
     * @param  string          $message
     * @param  int|string|null $size
     * @param  string          $align
     * @param  int             $innerPad
     * @param  bool            $newline
     * @return string
     */
    public function alertPrimary(string $message, int|string|null $size = null, string $align = 'center', int $innerPad = 4, bool $newline = true): string
    {
        return $this->alert($message, Color::BRIGHT_BOLD_WHITE, Color::BLUE, $size, $align, $innerPad, $newline);
    }

    /**
     * Build a "secondary" alert box
     *
     * @param  string          $message
     * @param  int|string|null $size
     * @param  string          $align
     * @param  int             $innerPad
     * @param  bool            $newline
     * @return string
     */
    public function alertSecondary(string $message, int|string|null $size = null, string $align = 'center', int $innerPad = 4, bool $newline = true): string
    {
        return $this->alert($message, Color::BRIGHT_BOLD_WHITE, Color::MAGENTA, $size, $align, $innerPad, $newline);
    }

    /**
     * Build a "dark" alert box
     *
     * @param  string          $message
     * @param  int|string|null $size
     * @param  string          $align
     * @param  int             $innerPad
     * @param  bool            $newline
     * @return string
     */
    public function alertDark(string $message, int|string|null $size = null, string $align = 'center', int $innerPad = 4, bool $newline = true): string
    {
        return $this->alert($message, Color::BRIGHT_BOLD_WHITE, Color::BRIGHT_BLACK, $size, $align, $innerPad, $newline);
    }

    /**
     * Build a "light" alert box
     *
     * @param  string          $message
     * @param  int|string|null $size
     * @param  string          $align
     * @param  int             $innerPad
     * @param  bool            $newline
     * @return string
     */
    public function alertLight(string $message, int|string|null $size = null, string $align = 'center', int $innerPad = 4, bool $newline = true): string
    {
        return $this->alert($message, Color::BOLD_BLACK, Color::WHITE, $size, $align, $innerPad, $newline);
    }

}
