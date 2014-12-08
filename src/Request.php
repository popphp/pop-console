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
