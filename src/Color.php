<?php
/**
 * Pop PHP Framework (https://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Console;

/**
 * Console color class
 *
 * @category   Pop
 * @package    Pop\Console
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2025 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    4.2.4
 */
class Color
{

    /**
     * Color indices
     */
    const NORMAL              = 0;
    const BLACK               = 1;
    const RED                 = 2;
    const GREEN               = 3;
    const YELLOW              = 4;
    const BLUE                = 5;
    const MAGENTA             = 6;
    const CYAN                = 7;
    const WHITE               = 8;
    const BRIGHT_BLACK        = 9;
    const BRIGHT_RED          = 10;
    const BRIGHT_GREEN        = 11;
    const BRIGHT_YELLOW       = 12;
    const BRIGHT_BLUE         = 13;
    const BRIGHT_MAGENTA      = 14;
    const BRIGHT_CYAN         = 15;
    const BRIGHT_WHITE        = 16;
    CONST BOLD_BLACK          = 17;
    CONST BOLD_RED            = 18;
    CONST BOLD_GREEN          = 19;
    CONST BOLD_YELLOW         = 20;
    CONST BOLD_BLUE           = 21;
    CONST BOLD_MAGENTA        = 22;
    CONST BOLD_CYAN           = 23;
    CONST BOLD_WHITE          = 24;
    CONST BRIGHT_BOLD_BLACK   = 25;
    CONST BRIGHT_BOLD_RED     = 26;
    CONST BRIGHT_BOLD_GREEN   = 27;
    CONST BRIGHT_BOLD_YELLOW  = 28;
    CONST BRIGHT_BOLD_BLUE    = 29;
    CONST BRIGHT_BOLD_MAGENTA = 30;
    CONST BRIGHT_BOLD_CYAN    = 31;
    CONST BRIGHT_BOLD_WHITE   = 32;

    /**
     * Color map of foreground ansi values
     *
     * @var array
     */
    protected static array $fgColorMap = [
        self::NORMAL              => '22;39',
        self::BLACK               => '0;30',
        self::RED                 => '0;31',
        self::GREEN               => '0;32',
        self::YELLOW              => '0;33',
        self::BLUE                => '0;34',
        self::MAGENTA             => '0;35',
        self::CYAN                => '0;36',
        self::WHITE               => '0;37',
        self::BRIGHT_BLACK        => '0;90',
        self::BRIGHT_RED          => '0;91',
        self::BRIGHT_GREEN        => '0;92',
        self::BRIGHT_YELLOW       => '0;93',
        self::BRIGHT_BLUE         => '0;94',
        self::BRIGHT_MAGENTA      => '0;95',
        self::BRIGHT_CYAN         => '0;96',
        self::BRIGHT_WHITE        => '0;97',
        self::BOLD_BLACK          => '1;30',
        self::BOLD_RED            => '1;31',
        self::BOLD_GREEN          => '1;32',
        self::BOLD_YELLOW         => '1;33',
        self::BOLD_BLUE           => '1;34',
        self::BOLD_MAGENTA        => '1;35',
        self::BOLD_CYAN           => '1;36',
        self::BOLD_WHITE          => '1;37',
        self::BRIGHT_BOLD_BLACK   => '1;90',
        self::BRIGHT_BOLD_RED     => '1;91',
        self::BRIGHT_BOLD_GREEN   => '1;92',
        self::BRIGHT_BOLD_YELLOW  => '1;93',
        self::BRIGHT_BOLD_BLUE    => '1;94',
        self::BRIGHT_BOLD_MAGENTA => '1;95',
        self::BRIGHT_BOLD_CYAN    => '1;96',
        self::BRIGHT_BOLD_WHITE   => '1;97',
    ];

    /**
     * Color map of background color ansi values
     *
     * @var array
     */
    protected static array $bgColorMap = [
        self::NORMAL         => '0;49',
        self::BLACK          => '40',
        self::RED            => '41',
        self::GREEN          => '42',
        self::YELLOW         => '43',
        self::BLUE           => '44',
        self::MAGENTA        => '45',
        self::CYAN           => '46',
        self::WHITE          => '47',
        self::BRIGHT_BLACK   => '100',
        self::BRIGHT_RED     => '101',
        self::BRIGHT_GREEN   => '102',
        self::BRIGHT_YELLOW  => '103',
        self::BRIGHT_BLUE    => '104',
        self::BRIGHT_MAGENTA => '105',
        self::BRIGHT_CYAN    => '106',
        self::BRIGHT_WHITE   => '107'
    ];

    /**
     * Colorize a string for output
     *
     * @param  string $string
     * @param  ?int   $fg
     * @param  ?int   $bg
     * @param  bool   $raw
     * @return string
     */
    public static function colorize(string $string, ?int $fg = null, ?int $bg = null, bool $raw = false): string
    {
        if ((stripos(PHP_OS, 'win') === false) && (!$raw)) {
            return static::getFgColorCode($fg) . static::getBgColorCode($bg) . $string . "\x1b[0m";
        } else {
            return $string;
        }
    }

    /**
     * Get the foreground color code from the color map
     *
     * @param  ?int $color
     * @return mixed
     */
    public static function getFgColorCode(?int $color = null): mixed
    {
        if (($color !== null) && isset(static::$fgColorMap[$color])) {
            return "\x1b[" . static::$fgColorMap[$color] . "m";
        }
        return null;
    }

    /**
     * Get the background color code from the color map
     *
     * @param  ?int $color
     * @return mixed
     */
    public static function getBgColorCode(int $color = null): mixed
    {
        if (($color !== null) && isset(static::$bgColorMap[$color])) {
            return "\x1b[" . static::$bgColorMap[$color] . "m";
        }
        return null;
    }

}
