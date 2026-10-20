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
 * Console help class
 *
 * @category   Pop
 * @package    Pop\Console
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2026 Nick Sagona, III
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.0
 */
class Help
{

    /**
     * Gap between a command label and its help text
     * @var string
     */
    protected string $helpGap = '    ';

    /**
     * Minimum width of help text a command must leave room for, within the wrap width,
     * to set the help column; longer commands get their help bumped to the next line
     * @var int
     */
    protected int $minHelpWidth = 40;

    /**
     * Set the gap between a command label and its help text
     *
     * @param  string $helpGap
     * @return static
     */
    public function setHelpGap(string $helpGap): static
    {
        $this->helpGap = $helpGap;
        return $this;
    }

    /**
     * Set the minimum help text width
     *
     * @param  int $minHelpWidth
     * @return static
     */
    public function setMinHelpWidth(int $minHelpWidth): static
    {
        $this->minHelpWidth = $minHelpWidth;
        return $this;
    }

    /**
     * Get the gap between a command label and its help text
     *
     * @return string
     */
    public function getHelpGap(): string
    {
        return $this->helpGap;
    }

    /**
     * Get the minimum help text width
     *
     * @return int
     */
    public function getMinHelpWidth(): int
    {
        return $this->minHelpWidth;
    }

    /**
     * Has a gap between a command label and its help text
     *
     * @return bool
     */
    public function hasHelpGap(): bool
    {
        return !empty($this->helpGap);
    }

    /**
     * Has a minimum help text width
     *
     * @return bool
     */
    public function hasMinHelpWidth(): bool
    {
        return !empty($this->minHelpWidth);
    }

    /**
     * Render the help screen body for a set of commands
     *
     * @param  array   $commands
     * @param  bool    $raw
     * @param  ?string $subCommand
     * @param  string  $indent
     * @param  ?int    $wrap
     * @param  array   $helpColors
     * @return string
     */
    public function render(array $commands, bool $raw, ?string $subCommand, string $indent, ?int $wrap, array $helpColors): string
    {
        $response       = '';
        $rows           = [];
        $commandLengths = [];

        foreach ($commands as $key => $command) {
            if ($this->matchesSubCommand($command, $subCommand)) {
                [$label, $length]     = $this->formatHelpLabel($command, $raw, $helpColors);
                $rows[$key]           = $indent . $label;
                $commandLengths[$key] = $length;
            }
        }

        // Only commands that leave room for $minHelpWidth of help text set the help column,
        // so one command with a long list of options doesn't squeeze every other row's help
        $columnLengths = array_filter($commandLengths, function ($length) use ($wrap) {
            return !$this->isTooLong($length, $wrap);
        });

        $maxLength = (!empty($columnLengths)) ? max($columnLengths) : 0;
        $wrapped   = false;
        $total     = count($rows);
        $i         = 0;

        foreach ($rows as $key => $row) {
            [$line, $wrapped] = $this->buildHelpRow(
                $commands[$key], $row, $commandLengths[$key], $maxLength, ($i == $total - 1), $wrapped, $indent, $wrap,
                $this->isTooLong($commandLengths[$key], $wrap)
            );
            $response .= $line;
            $i++;
        }

        return $response;
    }

    /**
     * Determine if a command label is too long to leave $minHelpWidth of help text on the same line
     *
     * @param  int  $length
     * @param  ?int $wrap
     * @return bool
     */
    protected function isTooLong(int $length, ?int $wrap): bool
    {
        return (($wrap !== null) && (($length + strlen($this->helpGap) + $this->minHelpWidth) > $wrap));
    }

    /**
     * Determine if a registered command belongs to the requested subcommand namespace
     *
     * Matches against the command's bare name with its registered script name (e.g. './app') stripped
     * off first, rather than the raw display key, so a subcommand like 'db' or 'db:' correctly matches
     * a command named 'db:migrate' without also matching an unrelated command registered under a script
     * name that merely happens to start with the same letters (e.g. a script called 'dbapp'). Command
     * naming conventions aren't assumed to use any particular delimiter (':', space, or otherwise) since
     * the script name is stripped by exact, known value rather than guessed from string structure.
     *
     * @param  Command\CommandInterface $command
     * @param  ?string                  $subCommand
     * @return bool
     */
    protected function matchesSubCommand(Command\CommandInterface $command, ?string $subCommand = null): bool
    {
        if (empty($subCommand)) {
            return true;
        }

        $name = (string)$command->getName();

        if ($command->hasScriptName()) {
            $prefix = $command->getScriptName() . ' ';
            if (str_starts_with($name, $prefix)) {
                $name = substr($name, strlen($prefix));
            }
        }

        return str_starts_with($name, $subCommand);
    }

    /**
     * Format a registered command's name/params into a colorized help label
     *
     * @param  Command\CommandInterface $command
     * @param  bool                     $raw
     * @param  array                    $helpColors
     * @return array
     */
    protected function formatHelpLabel(Command\CommandInterface $command, bool $raw, array $helpColors): array
    {
        $name   = $command->getName();
        $params = $command->getParams();
        $length = strlen((string)$name);

        if (count($helpColors) > 0) {
            $name = $this->colorizeHelpName((string)$name, $raw, $helpColors);
        }

        if ($params !== null) {
            $length += (strlen((string)$params) + 1);
            $name   .= $this->colorizeHelpParams($params, $raw, $helpColors);
        }

        return [$name, $length];
    }

    /**
     * Colorize a command name (and its sub-name, if space-separated) for the help screen
     *
     * @param  string $name
     * @param  bool   $raw
     * @param  array  $helpColors
     * @return string
     */
    protected function colorizeHelpName(string $name, bool $raw, array $helpColors): string
    {
        if (str_contains($name, ' ')) {
            $name1 = substr($name, 0, strpos($name, ' '));
            $name2 = substr($name, strpos($name, ' ') + 1);
            if (isset($helpColors[0])) {
                $name1 = Color::colorize($name1, $helpColors[0], null, $raw);
            }
            if (isset($helpColors[1])) {
                $name2 = Color::colorize($name2, $helpColors[1], null, $raw);
            }
            return $name1 . ' ' . $name2;
        } else if (isset($helpColors[0])) {
            return Color::colorize($name, $helpColors[0], null, $raw);
        }

        return $name;
    }

    /**
     * Colorize a command's params for the help screen
     *
     * @param  string $params
     * @param  bool   $raw
     * @param  array  $helpColors
     * @return string
     */
    protected function colorizeHelpParams(string $params, bool $raw, array $helpColors): string
    {
        if (str_contains($params, '-') && str_contains($params, '<')) {
            $pars        = explode(' ', $params);
            $optionFirst = str_contains($pars[0], '-');
            $colorIndex  = 2;
            $colored     = '';
            foreach ($pars as $p) {
                if (isset($helpColors[3]) &&
                    (($optionFirst) && str_contains($p, '<')) || ((!$optionFirst) && str_contains($p, '-'))) {
                    $colorIndex = 3;
                }
                $colored .= ' ' . ((isset($helpColors[$colorIndex])) ?
                        Color::colorize($p, $helpColors[$colorIndex], null, $raw) : $p);
            }
            return $colored;
        }

        return ' ' . ((isset($helpColors[2])) ?
                Color::colorize($params, $helpColors[2], null, $raw) : $params);
    }

    /**
     * Build one command's row for the help screen, wrapping its help text if needed
     *
     * @param  Command\CommandInterface $command
     * @param  string                   $label
     * @param  int                      $length
     * @param  int                      $maxLength
     * @param  bool                     $isLast
     * @param  bool                     $wrapped
     * @param  string                   $indent
     * @param  ?int                     $wrap
     * @param  bool                     $bumped
     * @return array
     */
    protected function buildHelpRow(
        Command\CommandInterface $command, string $label, int $length, int $maxLength, bool $isLast, bool $wrapped,
        string $indent, ?int $wrap, bool $bumped = false
    ): array
    {
        if (!$command->hasHelp()) {
            return [$label . $command->getHelp() . PHP_EOL, $wrapped];
        }

        $help = $command->getHelp();
        $pad  = ($length < $maxLength) ?
            str_repeat(' ', $maxLength - $length) . $this->helpGap : $this->helpGap;

        if (($wrap === null) || (!$bumped && (strlen((string)$command . $pad . $help) <= $wrap))) {
            return [$label . $pad . $help . PHP_EOL, false];
        }

        $row        = ($wrapped) ? '' : PHP_EOL;
        $helpIndent = $indent . str_repeat(' ', $maxLength + strlen($this->helpGap));
        $offset     = max(1, $wrap - $maxLength - strlen($this->helpGap));
        $lines      = explode(PHP_EOL, wordwrap($help, $offset, PHP_EOL));

        if ($bumped) {
            $row .= $label . PHP_EOL;
        }

        foreach ($lines as $lineIndex => $line) {
            $row .= (($lineIndex == 0) && !$bumped) ?
                $label . $pad . $line . PHP_EOL :
                $helpIndent . $line . PHP_EOL;
        }

        if (!$isLast) {
            $row .= PHP_EOL;
        }

        return [$row, true];
    }

}
