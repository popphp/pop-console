<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2016 NOLA Interactive, LLC. (http://www.nolainteractive.com)
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
 * @copyright  Copyright (c) 2009-2016 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    3.0.0
 */
class Command
{

    /**
     * Command name
     * @var string
     */
    protected $name = null;

    /**
     * Command help
     * @var string
     */
    protected $help = null;

    /**
     * Instantiate the command object
     *
     * @param  string $name
     * @param  string $help
     * @return Command
     */
    public function __construct($name, $help = null)
    {
        $this->name = $name;
        $this->help = $help;
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
     * Get the command help
     *
     * @return string
     */
    public function getHelp()
    {
        return $this->help;
    }

    /**
     * Return the command name as string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

}