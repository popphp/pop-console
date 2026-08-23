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
 * Console header class
 *
 * @category   Pop
 * @package    Pop\Console
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2026 Nick Sagona, III
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.0
 */
class Header
{

    use MessageTrait;

    /**
     * Instantiate the header object
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
     * Build a horizontal line rule
     *
     * @param  string $char
     * @param  ?int   $size
     * @param  bool   $newline
     * @return string
     */
    public function line(string $char = '-', ?int $size = null, bool $newline = true): string
    {
        $line = '';

        if ($size === null) {
            if (!empty($this->wrap)) {
                $size = $this->wrap;
            } else if (!empty($this->width)) {
                $size = $this->width - ((int)$this->margin * 2);
            }
        }

        $line .= $this->indent . str_repeat($char, $size);

        if ($newline) {
            $line .= PHP_EOL;
        }

        return $line;
    }

    /**
     * Build a header
     *
     * @param  string          $string
     * @param  string          $char
     * @param  int|string|null $size
     * @param  string          $align
     * @param  bool            $newline
     * @return string
     */
    public function header(
        string $string, string $char = '-', int|string|null $size = null, string $align = 'left', bool $newline = true
    ): string
    {
        $header = '';
        $size   = $this->resolveSize($string, $size, $this->wrap, $this->width, $this->margin);

        if (strlen($string) > $size) {
            $lines = explode(PHP_EOL, wordwrap($string, $size, PHP_EOL));
            foreach ($lines as $line) {
                if (($align != 'left') && (strlen($line) < $size)) {
                    $line = str_repeat(' ', $this->calculatePad($line, $size, $align)) . $line;
                }
                $header .= $this->indent . $line . PHP_EOL;
            }
        } else {
            if (($align != 'left') && (strlen($string) < $size)) {
                $string = str_repeat(' ', $this->calculatePad($string, $size, $align)) . $string;
            }
            $header = $this->indent . $string . PHP_EOL;
        }

        $header .= $this->line($char, $size, $newline);

        return $header;
    }

    /**
     * Build a left-aligned header
     *
     * @param  string          $string
     * @param  string          $char
     * @param  int|string|null $size
     * @param  bool            $newline
     * @return string
     */
    public function headerLeft(string $string, string $char = '-', int|string|null $size = 'auto', bool $newline = true): string
    {
        return $this->header($string, $char, $size, 'left', $newline);
    }

    /**
     * Build a center-aligned header
     *
     * @param  string          $string
     * @param  string          $char
     * @param  int|string|null $size
     * @param  bool            $newline
     * @return string
     */
    public function headerCenter(string $string, string $char = '-', int|string|null $size = 'auto', bool $newline = true): string
    {
        return $this->header($string, $char, $size, 'center', $newline);
    }

    /**
     * Build a right-aligned header
     *
     * @param  string          $string
     * @param  string          $char
     * @param  int|string|null $size
     * @param  bool            $newline
     * @return string
     */
    public function headerRight(string $string, string $char = '-', int|string|null $size = 'auto', bool $newline = true): string
    {
        return $this->header($string, $char, $size, 'right', $newline);
    }

}
