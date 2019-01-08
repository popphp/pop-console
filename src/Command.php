<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2019 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Console;

/**
 * Console command  class
 *
 * @category   Pop
 * @package    Pop\Console
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2019 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.1.0
 */
class Command
{

    /**
     * Command name
     * @var string
     */
    protected $name = null;

    /**
     * Command params
     * @var string
     */
    protected $params = null;

    /**
     * Command help
     * @var string
     */
    protected $help = null;

    /**
     * Instantiate the command object
     *
     * @param  string $name
     * @param  string $params
     * @param  string $help
     * @return Command
     */
    public function __construct($name, $params = null, $help = null)
    {
        $this->name   = $name;
        $this->params = $params;
        $this->help   = $help;
        return $this;
    }

    /**
     * Set the command name
     *
     * @param  string $name
     * @return Command
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Set the command params
     *
     * @param  string $params
     * @return Command
     */
    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * Set the command help
     *
     * @param  string $help
     * @return Command
     */
    public function setHelp($help)
    {
        $this->help = $help;
        return $this;
    }

    /**
     * Get the command name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the command params
     *
     * @return string
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Determine if the command has params
     *
     * @return boolean
     */
    public function hasParams()
    {
        return (null !== $this->params);
    }

    /**
     * Get the command help
     *
     * @return string
     */
    public function getHelp()
    {
        return $this->help;
    }

    /**
     * Determine if the command has help
     *
     * @return boolean
     */
    public function hasHelp()
    {
        return (null !== $this->help);
    }

    /**
     * Return the command name as string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name . ((null !== $this->params) ? ' ' . $this->params : null);
    }

}