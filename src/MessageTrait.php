<?php
declare(strict_types=1);
/**
 * Pop PHP Framework (https://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2027 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Console;

/**
 * Console message sizing/alignment trait, shared by Header and Alert
 *
 * @category   Pop
 * @package    Pop\Console
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2027 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.0
 */
trait MessageTrait
{

    /**
     * Resolve an explicit/auto/null $size against the given wrap/width/margin
     *
     * @param  string          $text
     * @param  int|string|null $size
     * @param  ?int            $wrap
     * @param  int             $width
     * @param  ?int            $margin
     * @param  int             $fallbackPadding
     * @return int|string
     */
    protected function resolveSize(string $text, int|string|null $size, ?int $wrap, int $width, ?int $margin, int $fallbackPadding = 0): int|string
    {
        if ($size === null) {
            if (!empty($wrap) && (strlen($text) > $wrap)) {
                $size = $wrap;
            } else if (!empty($width) && (strlen($text) > $width)) {
                $size = $width - ((int)$margin * 2);
            } else {
                $size = strlen($text) + $fallbackPadding;
            }
        } else if ($size == 'auto') {
            if (!empty($wrap)) {
                $size = $wrap;
            } else if (!empty($width)) {
                $size = $width - ((int)$margin * 2);
            }
        }

        return $size;
    }

    /**
     * Word-wrap and align a message into padded lines of $size width
     *
     * @param  string $message
     * @param  int    $size
     * @param  string $align
     * @param  int    $innerPad
     * @return array
     */
    protected function buildAlignedMessageLines(string $message, int $size, string $align, int $innerPad): array
    {
        $innerSize    = $size - ($innerPad * 2);
        $messageLines = [];
        $lines        = (strlen($message) > $innerSize) ?
            explode(PHP_EOL, wordwrap($message, $innerSize, PHP_EOL)) : [$message];

        foreach ($lines as $line) {
            $pad = $this->calculatePad($line, $size, $align);
            if ($align == 'center') {
                $messageLines[] = str_repeat(' ', $pad) . $line . str_repeat(' ', ($size - strlen($line) - $pad));
            } else if ($align == 'left') {
                $messageLines[] = str_repeat(' ', $innerPad) . $line . str_repeat(' ', ($size - strlen($line) - $pad - $innerPad));
            } else if ($align == 'right') {
                $messageLines[] = str_repeat(' ', ($size - strlen($line) - $innerPad)) . $line . str_repeat(' ', $innerPad);
            }
        }

        return $messageLines;
    }

    /**
     * Calculate string pad
     *
     * @param  string $string
     * @param  int    $size
     * @param  string $align
     * @return int
     */
    protected function calculatePad(string $string, int $size, string $align = 'center'): int
    {
        $pad = 0;

        if ($align == 'center') {
            $pad = (int)round(($size - strlen($string)) / 2);
        } else if ($align == 'right') {
            $pad = $size - strlen($string);
        }

        return $pad;
    }

}
