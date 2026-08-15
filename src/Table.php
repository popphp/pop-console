<?php
declare(strict_types=1);
/**
 * Pop PHP Framework (https://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2027 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Console;

/**
 * Console table class
 *
 * @category   Pop
 * @package    Pop\Console
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2027 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.0
 */
class Table
{

    /**
     * Table headers
     * @var array
     */
    protected array $headers = [];

    /**
     * Table rows
     * @var array
     */
    protected array $rows = [];

    /**
     * Horizontal border character
     * @var string
     */
    protected string $h = '-';

    /**
     * Vertical border character (null omits vertical dividers)
     * @var ?string
     */
    protected ?string $v = '|';

    /**
     * Header row foreground color
     * @var ?int
     */
    protected ?int $headerFg = null;

    /**
     * Header row background color
     * @var ?int
     */
    protected ?int $headerBg = null;

    /**
     * Instantiate the table object
     *
     * @param  array   $headers
     * @param  array   $rows
     * @param  string  $h
     * @param  ?string $v
     */
    public function __construct(array $headers = [], array $rows = [], string $h = '-', ?string $v = '|')
    {
        $this->setHeaders($headers);
        $this->setRows($rows);
        $this->h = $h;
        $this->v = $v;
    }

    /**
     * Set the table headers
     *
     * @param  array $headers
     * @return Table
     */
    public function setHeaders(array $headers): Table
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * Add a row to the table
     *
     * @param  array $row
     * @return Table
     */
    public function addRow(array $row): Table
    {
        $this->rows[] = $row;
        return $this;
    }

    /**
     * Set the table rows
     *
     * @param  array $rows
     * @return Table
     */
    public function setRows(array $rows): Table
    {
        $this->rows = $rows;
        return $this;
    }

    /**
     * Set the header row color
     *
     * @param  ?int $fg
     * @param  ?int $bg
     * @return Table
     */
    public function setHeaderColor(?int $fg, ?int $bg = null): Table
    {
        $this->headerFg = $fg;
        $this->headerBg = $bg;
        return $this;
    }

    /**
     * Render the table
     *
     * @return string
     */
    public function render(): string
    {
        $widths = $this->getColumnWidths();

        if (empty($widths)) {
            return '';
        }

        $hasHeaders = !empty($this->headers);
        $output     = $this->buildBorderRow($widths);

        if ($hasHeaders) {
            $output .= $this->buildDataRow($this->headers, $widths, true);
            $output .= $this->buildBorderRow($widths);
        }

        foreach ($this->rows as $row) {
            $output .= $this->buildDataRow($row, $widths, false);
        }

        $output .= $this->buildBorderRow($widths);

        return $output;
    }

    /**
     * Calculate the column widths from raw (uncolored) header/row content
     *
     * @return array
     */
    protected function getColumnWidths(): array
    {
        $widths = [];

        foreach ($this->headers as $i => $header) {
            $widths[$i] = strlen((string)$header);
        }

        foreach ($this->rows as $row) {
            foreach ($row as $i => $cell) {
                $length = strlen((string)$cell);
                if (!isset($widths[$i]) || ($length > $widths[$i])) {
                    $widths[$i] = $length;
                }
            }
        }

        ksort($widths);

        return $widths;
    }

    /**
     * Build a horizontal border row
     *
     * @param  array $widths
     * @return string
     */
    protected function buildBorderRow(array $widths): string
    {
        if (empty($this->v)) {
            $total = array_sum($widths) + (count($widths) * 2) + (count($widths) - 1);
            return str_repeat($this->h, $total) . PHP_EOL;
        }

        $segments = [];
        foreach ($widths as $width) {
            $segments[] = str_repeat($this->h, $width + 2);
        }

        return '+' . implode('+', $segments) . '+' . PHP_EOL;
    }

    /**
     * Build a header or data row
     *
     * @param  array $cells
     * @param  array $widths
     * @param  bool  $isHeader
     * @return string
     */
    protected function buildDataRow(array $cells, array $widths, bool $isHeader = false): string
    {
        $rendered = [];

        foreach ($widths as $i => $width) {
            $text = (string)($cells[$i] ?? '');
            $pad  = str_pad($text, $width);

            if ($isHeader && (($this->headerFg !== null) || ($this->headerBg !== null))) {
                $pad = Color::colorize($pad, $this->headerFg, $this->headerBg);
            }

            $rendered[] = ' ' . $pad . ' ';
        }

        if (empty($this->v)) {
            return implode(' ', $rendered) . PHP_EOL;
        }

        return $this->v . implode($this->v, $rendered) . $this->v . PHP_EOL;
    }

}
