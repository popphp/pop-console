<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp
 * @category   Pop
 * @package    Pop_Console
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2015 NOLA Interactive, LLC. (http://www.nolainteractive.com)
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
 * @copyright  Copyright (c) 2009-2015 NOLA Interactive, LLC. (http://www.nolainteractive.com)
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
     * Required parameters that were not passed
     * @var array
     */
    protected $requiredParamsNotFound = [];

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
     * Determine if the request is valid
     *
     * @return boolean
     */
    public function isValid()
    {
        return (count($this->requiredParamsNotFound) == 0);
    }

    /**
     * Determine if the request has been parsed yet or not
     *
     * @return boolean
     */
    public function isParsed()
    {
        return $this->parsed;
    }

    /**
     * Get the required parameters not found
     *
     * @return array
     */
    public function getRequiredParamsNotFound()
    {
        return $this->requiredParamsNotFound;
    }

    /**
     * Parse the request
     *
     * @param  array $commands
     * @param  array $options
     * @return void
     */
    public function parse(array $commands = [], array $options = [])
    {
        foreach ($options as $name => $option) {
            if (null === $option->getValue()) {
                $required      = $option->isRequired();
                $valueOptional = $option->isValueOptional();
                $valueRequired = $option->isValueRequired();
                $valueIsArray  = $option->isValueArray();
                $longOption    = false;

                $optionsAry  = [];
                $optionFound = false;

                if (count($this->args) > 0) {
                    foreach ($this->args as $key => $arg) {
                        if (($option->hasLongName()) && ($option->hasShortName())) {
                            $optionsAry[$option->getShortName()] = substr($option->getShortName(), 1);
                            $optionsAry[$option->getLongName()] = substr($option->getLongName(), 2);
                            $longOption = true;
                        } else if ($option->hasLongName()) {
                            $optionsAry[$option->getLongName()] = substr($option->getLongName(), 2);
                            $longOption = true;
                        } else {
                            $optionsAry[$option->getShortName()] = substr($option->getShortName(), 1);
                        }

                        foreach ($optionsAry as $opt => $name) {
                            if (substr($arg, 0, strlen($opt)) == $opt) {
                                $optionFound = true;
                                // Has value
                                if (($valueOptional) || ($valueRequired)) {
                                    if (($longOption) && (strpos($arg, '=') !== false)) {
                                        $optionValue = substr($arg, (strpos($arg, '=') + 1));
                                    } else {
                                        $optionValue = (strlen($arg) > strlen($opt)) ?
                                            substr($arg, strlen($opt)) : '';
                                    }

                                    // If value is an array
                                    if ($valueIsArray) {
                                        $optionValue = (strpos($optionValue, ',') !== false) ?
                                            explode(',', $optionValue) : [$optionValue];
                                    }

                                    // If option is required or value is required, set fail
                                    if ((($required) || ($valueRequired)) && (($optionValue == '') ||
                                            (is_array($optionValue) && isset($optionValue[0]) && empty($optionValue[0])))) {
                                        $optionFound = false;
                                    }
                                } else {
                                    $optionValue = true;
                                }
                                if ($optionFound) {
                                    if ($optionValue == '') {
                                        $optionValue = true;
                                    }
                                    $option->setValue($optionValue);
                                    unset($this->args[$key]);
                                }
                            }
                        }
                    }

                    if (!($optionFound)) {
                        $reqOptName = null;
                        if (($option->hasShortName()) && ($option->hasLongName())) {
                            $reqOptName = $option->getShortName() . '|' . $option->getLongName();
                        } else {
                            $reqOptName = ($option->hasLongName()) ? $option->getLongName() : $option->getShortName();
                        }
                        if (!in_array($reqOptName, $this->requiredParamsNotFound)) {
                            $this->requiredParamsNotFound[] = $reqOptName;
                        }
                    }
                } else if ($required) {
                    $reqOptName = null;
                    if (($option->hasShortName()) && ($option->hasLongName())) {
                        $reqOptName = $option->getShortName() . '|' . $option->getLongName();
                    } else {
                        $reqOptName = ($option->hasLongName()) ? $option->getLongName() : $option->getShortName();
                    }
                    if (!in_array($reqOptName, $this->requiredParamsNotFound)) {
                        $this->requiredParamsNotFound[] = $reqOptName;
                    }
                }
            }
        }

        // Clean up the arguments array
        $args = [];
        foreach ($this->args as $arg) {
            $args[] = $arg;
        }
        $this->args = $args;

        $isOverride = false;
        foreach ($commands as $name => $command) {
            if (in_array($name, $this->args)) {
                if ($command->isOverride()) {
                    $isOverride = true;
                }
                // Has value
                if (($command->isValueOptional()) || ($command->isValueRequired())) {
                    $key = array_search($name, $this->args);
                    $value = (isset($this->args[$key + 1]) && !in_array($this->args[$key + 1], $commands)) ?
                        $this->args[$key + 1] : '';

                    if ($command->isValueArray()) {
                        $value = (strpos($value, ',') !== false) ? explode(',', $value) : [$value];
                    }

                    if (($command->isValueRequired()) && (($value == '') ||
                            (is_array($value) && isset($value[0]) && empty($value[0])))) {
                        $this->requiredParamsNotFound[] = $name;
                    } else {
                        if ($value == '') {
                            $value = true;
                        }
                        $command->setValue($value);
                    }
                } else {
                    $command->setValue(true);
                }
            }
        }

        if ($isOverride) {
            $this->requiredParamsNotFound = [];
        }

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
