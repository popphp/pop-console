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
namespace Pop\Console\Input;

/**
 * Console input command class
 *
 * @category   Pop
 * @package    Pop_Console
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2014 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    2.0.0a
 */
class Command extends AbstractInput
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
     * Instantiate a new command object
     *
     * @param  string $name
     * @param  string  $valueMode
     * @return Command
     */
    public function __construct($name, $valueMode = null)
    {
        $this->setName($name);
        if (null !== $valueMode) {
            $this->setValueMode($valueMode);
        }
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

}