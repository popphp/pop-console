<?php
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
 * Console progress bar class
 *
 * @category   Pop
 * @package    Pop\Console
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2027 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.0
 */
class ProgressBar
{

    /**
     * Total number of steps
     * @var int
     */
    protected int $total;

    /**
     * Current step
     * @var int
     */
    protected int $current = 0;

    /**
     * Width of the bar's fill portion
     * @var int
     */
    protected int $width = 28;

    /**
     * Character used for the filled portion of the bar
     * @var string
     */
    protected string $barChar = '=';

    /**
     * Character used at the leading edge of the filled portion
     * @var string
     */
    protected string $progressChar = '>';

    /**
     * Character used for the unfilled portion of the bar
     * @var string
     */
    protected string $emptyChar = ' ';

    /**
     * Optional leading message
     * @var ?string
     */
    protected ?string $message = null;

    /**
     * Indent prefix
     * @var string
     */
    protected string $indent = '';

    /**
     * Foreground color of the filled portion
     * @var ?int
     */
    protected ?int $fg = null;

    /**
     * Background color of the filled portion
     * @var ?int
     */
    protected ?int $bg = null;

    /**
     * Flag for if the progress bar has finished
     * @var bool
     */
    protected bool $finished = false;

    /**
     * Instantiate the progress bar object
     *
     * @param  int     $total
     * @param  ?string $message
     * @param  int     $width
     * @throws Exception
     */
    public function __construct(int $total, ?string $message = null, int $width = 28)
    {
        if ($total <= 0) {
            throw new Exception('The total number of steps must be greater than zero.');
        }

        $this->total   = $total;
        $this->message = $message;
        $this->width   = $width;
    }

    /**
     * Set the leading message
     *
     * @param  ?string $message
     * @return ProgressBar
     */
    public function setMessage(?string $message): ProgressBar
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Set the width of the bar's fill portion
     *
     * @param  int $width
     * @return ProgressBar
     */
    public function setWidth(int $width): ProgressBar
    {
        $this->width = $width;
        return $this;
    }

    /**
     * Set the bar characters
     *
     * @param  string $barChar
     * @param  string $progressChar
     * @param  string $emptyChar
     * @return ProgressBar
     */
    public function setChars(string $barChar = '=', string $progressChar = '>', string $emptyChar = ' '): ProgressBar
    {
        $this->barChar      = $barChar;
        $this->progressChar = $progressChar;
        $this->emptyChar    = $emptyChar;
        return $this;
    }

    /**
     * Set the color of the filled portion of the bar
     *
     * @param  ?int $fg
     * @param  ?int $bg
     * @return ProgressBar
     */
    public function setColor(?int $fg, ?int $bg = null): ProgressBar
    {
        $this->fg = $fg;
        $this->bg = $bg;
        return $this;
    }

    /**
     * Set the indent prefix
     *
     * @param  string $indent
     * @return ProgressBar
     */
    public function setIndent(string $indent): ProgressBar
    {
        $this->indent = $indent;
        return $this;
    }

    /**
     * Advance the progress bar by the given number of steps
     *
     * @param  int $step
     * @return ProgressBar
     */
    public function advance(int $step = 1): ProgressBar
    {
        return $this->setProgress($this->current + $step);
    }

    /**
     * Set the current progress
     *
     * @param  int $current
     * @return ProgressBar
     */
    public function setProgress(int $current): ProgressBar
    {
        $this->current = max(0, min($this->total, $current));
        $this->render();
        return $this;
    }

    /**
     * Finish the progress bar
     *
     * @return ProgressBar
     */
    public function finish(): ProgressBar
    {
        $this->current  = $this->total;
        $this->finished = true;
        $this->render();
        echo PHP_EOL;
        return $this;
    }

    /**
     * Get the current progress
     *
     * @return int
     */
    public function getCurrent(): int
    {
        return $this->current;
    }

    /**
     * Get the total number of steps
     *
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * Determine if the progress bar has finished
     *
     * @return bool
     */
    public function isFinished(): bool
    {
        return $this->finished;
    }

    /**
     * Render the progress bar to the console
     *
     * @return void
     */
    protected function render(): void
    {
        echo "\r" . $this->buildLine();
    }

    /**
     * Build the progress bar line
     *
     * @return string
     */
    protected function buildLine(): string
    {
        $percent = (int)round(($this->current / $this->total) * 100);
        $filled  = (int)round(($this->current / $this->total) * $this->width);
        $filled  = max(0, min($this->width, $filled));

        if ($filled >= $this->width) {
            $bar = str_repeat($this->barChar, $this->width);
        } else if ($filled <= 0) {
            $bar = str_repeat($this->emptyChar, $this->width);
        } else {
            $bar = str_repeat($this->barChar, $filled - 1) . $this->progressChar .
                str_repeat($this->emptyChar, $this->width - $filled);
        }

        if (($this->fg !== null) || ($this->bg !== null)) {
            $bar = Color::colorize($bar, $this->fg, $this->bg);
        }

        $message = ($this->message !== null) ? $this->message . ' ' : '';

        return $this->indent . $message . '[' . $bar . ']' .
            sprintf(' %3d%% (%d/%d)  ', $percent, $this->current, $this->total);
    }

}
