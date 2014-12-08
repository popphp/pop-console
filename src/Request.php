<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp
 * @category   Pop
 * @package    Pop_Console
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2014 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Console;

/**
 * Console request class
 *
 * @category   Pop
 * @package    Pop_Console
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2014 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    2.0.0a
 */
class Request
{

    /**
     * Console arguments
     * @var array
     */
    protected $args = [];

    /**
     * Console environment variables
     * @var array
     */
    protected $env = [];

    /**
     * Console script name
     * @var string
     */
    protected $scriptName = null;

    /**
     * Console option values
     * @var array
     */
    protected $options = [];

    /**
     * Required options that were not passed
     * @var array
     */
    protected $requiredOptionsNotFound = [];

    /**
     * Parsed flag
     * @var boolean
     */
    protected $parsed = false;

    /**
     * Instantiate a new console request object
     *
     * @throws Exception
     * @return Request
     */
    public function __construct()
    {
        if (!isset($_SERVER['argv'])) {
            throw new Exception('Error: The command line arguments are not set.');
        }
        $this->setArguments($_SERVER['argv']);
        $this->setEnv($_ENV);
    }

    /**
     * Get a value from arguments array
     *
     * @param  string $key
     * @return mixed
     */
    public function getArgument($key)
    {
        return (isset($this->args[$key])) ? $this->args[$key] : null;
    }

    /**
     * Get the arguments array
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->args;
    }

    /**
     * Get a value from environment variables array
     *
     * @param  string $key
     * @return mixed
     */
    public function getEnv($key)
    {
        return (isset($this->env[$key])) ? $this->env[$key] : null;
    }

    /**
     * Get the environment variables array
     *
     * @return array
     */
    public function getEnvs()
    {
        $this->env;
    }

    /**
     * Get the script name
     *
     * @return string
     */
    public function getScriptName()
    {
        $this->scriptName;
    }

    /**
     * Get the options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Determine if the request is valid
     *
     * @return boolean
     */
    public function isValid()
    {
        return (count($this->requiredOptionsNotFound) == 0);
    }

    /**
     * Determine if the request has been parsed yet ornot
     *
     * @return boolean
     */
    public function isParsed()
    {
        return $this->parsed;
    }

    /**
     * Get the required options not found
     *
     * @return array
     */
    public function getRequiredOptionsNotFound()
    {
        return $this->requiredOptionsNotFound;
    }

    /**
     * Parse the request
     *
     * @param  $options
     * @return void
     */
    public function parse(array $options = [])
    {
        foreach ($options as $option) {
            if (substr($option, 0, 1) == '-') {
                $required   = false;
                $hasValue   = false;
                $longOption = false;
                $optionsAry = [];

                // If required
                if (substr($option, -1) == ':') {
                    $required = true;
                    $option   = substr($option, 0, -1);
                }
                // If has value
                if (substr($option, -1) == '=') {
                    $hasValue = true;
                    $option   = substr($option, 0, -1);
                }
                // If long option
                if (substr($option, 0, 2) == '--') {
                    $optionName = substr($option, 2);
                    $longOption = true;
                // Else, if short option
                } else {
                    $optionName = substr($option, 1);
                }
                // If there are alternates
                if (strpos($option, '|') !== false){
                    $alts = explode('|', $option);
                    if (isset($alts[0]) && isset($alts[1])) {
                        $optionsAry[$alts[0]] = substr($alts[0], 1);
                        $optionsAry[$alts[1]] = substr($alts[1], 2);
                    }
                } else {
                    $optionsAry[$option] = $optionName;
                }


                $optionFound = false;
                foreach ($this->args as $key => $arg) {
                    foreach ($optionsAry as $opt => $name) {
                        if (substr($arg, 0, strlen($opt)) == $opt) {
                            $optionFound = true;
                            if ($hasValue) {
                                if (($longOption) && (strpos($arg, '=') !== false)) {
                                    $optionValue = substr($arg, (strpos($arg, '=') + 1));
                                } else {
                                    $optionValue = substr($arg, strlen($opt));
                                }
                                if (($required) && ($optionValue == '')) {
                                    $optionFound = false;
                                }
                            } else {
                                $optionValue = true;
                            }
                            $this->options[$name] = $optionValue;
                            unset($this->args[$key]);
                        }
                    }
                }

                if (($required) && !($optionFound)) {
                    $this->requiredOptionsNotFound[] = $option;
                }
            }
        }

        // Clean up the arguments array
        $args = [];
        foreach ($this->args as $arg) {
            $args[] = $arg;
        }
        $this->args = $args;

        $this->parsed = true;
    }

    /**
     * Set the console arguments
     *
     * @param  array $args
     * @return Request
     */
    protected function setArguments(array $args)
    {
        $this->scriptName = array_shift($args);
        $this->args = $args;
        return $this;
    }

    /**
     * Set the environment variables
     *
     * @param  array $env
     * @return Request
     */
    protected function setEnv(array $env)
    {
        $this->env = $env;
        return $this;
    }

}
