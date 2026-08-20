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
 * Console prompt class
 *
 * @category   Pop
 * @package    Pop\Console
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2027 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.0
 */
class Prompt
{

    /**
     * Instantiate the prompt object
     *
     * @param string  $indent
     * @param ?string $formattedHeader
     * @param mixed   $inputStream
     */
    public function __construct(
        protected string $indent = '', protected ?string $formattedHeader = null, protected mixed $inputStream = null
    ) { }

    /**
     * Get input from the prompt
     *
     * @param  string $prompt
     * @param  ?array $options
     * @param  bool   $caseSensitive
     * @param  int    $length
     * @return string
     */
    public function prompt(string $prompt, ?array $options = null, bool $caseSensitive = false, int $length = 500): string
    {
        if ($this->formattedHeader !== null) {
            echo $this->formattedHeader . $this->indent . $prompt;
        } else {
            echo $this->indent . $prompt;
        }

        $input = null;

        if ($options !== null) {
            $length = 0;
            foreach ($options as $key => $value) {
                $options[$key] = ($caseSensitive) ? $value : strtolower((string)$value);
                if (strlen((string)$value) > $length) {
                    $length = strlen((string)$value);
                }
            }

            while (!in_array($input, $options)) {
                if ($input !== null) {
                    echo $this->indent . $prompt;
                }
                $input = $this->getPromptInput($prompt, $length, $caseSensitive);

                // Empty input is what a closed/exhausted input stream produces on every
                // read, so treat it the same as promptMulti() does: stop retrying and
                // return it rather than spin forever re-reading a stream that can't
                // ever satisfy $options.
                if ($input === '') {
                    return $input;
                }
            }
        } else {
            while ($input === null) {
                $input = $this->getPromptInput($prompt, $length, $caseSensitive);
            }
        }

        return $input;
    }

    /**
     * Display a prompt that accepts one or more comma-separated selections
     *
     * @param  string $prompt
     * @param  array  $options
     * @param  bool   $caseSensitive
     * @param  int    $length
     * @return array
     */
    public function promptMulti(string $prompt, array $options, bool $caseSensitive = false, int $length = 500): array
    {
        foreach ($options as $key => $value) {
            $options[$key] = ($caseSensitive) ? (string)$value : strtolower((string)$value);
        }

        if ($this->formattedHeader !== null) {
            echo $this->formattedHeader . $this->indent . $prompt;
        } else {
            echo $this->indent . $prompt;
        }

        $selected = null;

        while ($selected === null) {
            $input  = $this->getPromptInput($prompt, $length, $caseSensitive);
            $tokens = array_values(array_filter(
                array_map('trim', explode(',', $input)),
                fn($token) => $token !== ''
            ));

            // Empty input is a valid "no selection" answer, and is also what a
            // closed input stream produces. Returning here is what keeps EOF
            // from spinning the retry loop forever.
            if (empty($tokens)) {
                return [];
            }

            if (empty(array_diff($tokens, $options))) {
                $selected = array_values(array_unique($tokens));
            } else {
                echo $this->indent . $prompt;
            }
        }

        return $selected;
    }

    /**
     * Display confirm message prompt
     *
     * @param  string $message
     * @param  array  $options
     * @param  bool   $caseSensitive
     * @param  int    $length
     * @param  bool   $exit
     * @return string
     */
    public function confirm(
        string $message = 'Are you sure?', array $options = ['Y', 'N'], bool $caseSensitive = false,
        int $length = 500, bool $exit = true
    ): string
    {
        $message .= ' [' . implode('/', $options) . '] ';
        $response = $this->prompt($message, $options, $caseSensitive, $length);

        if (($exit) && ((strtolower($response) == 'n') || (strtolower($response) == 'no'))) {
            echo PHP_EOL;
            exit(127);
        }

        return $response;
    }

    /**
     * Get prompt input
     *
     * @param  string $prompt
     * @param  int    $length
     * @param  bool   $caseSensitive
     * @return string
     */
    protected function getPromptInput(string $prompt, int $length = 500, bool $caseSensitive = false): string
    {
        $stream = $this->inputStream ?? fopen('php://stdin', 'r');
        $input  = fgets($stream, strlen((string)$prompt) + $length);
        $input  = ($caseSensitive) ? rtrim((string)$input) : strtolower(rtrim((string)$input));

        if ($this->inputStream === null) {
            fclose($stream);
        }

        return $input;
    }

}
