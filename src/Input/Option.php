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
namespace Pop\Console\Input;

/**
 * Console input command class
 *
 * @category   Pop
 * @package    Pop_Console
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2015 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    2.0.0a
 */
class Option extends AbstractInput
{

    /**
     * Option short name
     * @var string
     */
    protected $shortName = null;

    /**
     * Option long name
     * @var string
     */
    protected $longName = null;

    /**
     * Option is required flag
     * @var boolean
     */
    protected $required = false;

    /**
     * Instantiate a new command object
     *
     * @param  string  $name
     * @param  string  $valueMode
     * @param  boolean $required
     * @return Option
     */
    public function __construct($name, $valueMode = null, $required = false)
    {
        $this->setName($name);
        if (null !== $valueMode) {
            $this->setValueMode($valueMode);
        }
        $this->setRequired($required);
    }

    /**
     * Set the option name
     *
     * @param  string $name
     * @throws \InvalidArgumentException
     * @return Option
     */
    public function setName($name)
    {
        // If both
        if (strpos($name, '|')) {
            $name = explode('|', $name);
            if ((substr($name[0], 0, 1) == '-') && (substr($name[1], 0, 2) == '--')) {
                $this->shortName = $name[0];
                $this->longName  = $name[1];
            } else {
                throw new \InvalidArgumentException(
                    'Error: The option name must be either a short option or long option, with dashes (-o, --option or -o|--option).'
                );
            }
        // If long
        } else if (substr($name, 0, 2) == '--') {
            $this->longName  = $name;
        // If short
        } else if (substr($name, 0, 1) == '-') {
            $this->shortName = $name;
        // Else, throw exception
        } else {
            throw new \InvalidArgumentException(
                'Error: The option name must be either a short option or long option, with dashes (-o, --option or -o|--option).'
            );
        }

        return $this;
    }

    /**
     * Set if the option is required
     *
     * @param  boolean $required
     * @return Option
     */
    public function setRequired($required)
    {
        $this->required = (bool)$required;
        return $this;
    }

    /**
     * Get the option short name
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * Get the option long name
     *
     * @return string
     */
    public function getLongName()
    {
        return $this->longName;
    }

    /**
     * Determine whether the option has a short name
     *
     * @return boolean
     */
    public function hasShortName()
    {
        return (null !== $this->shortName);
    }

    /**
     * Determine whether the option has a long name
     *
     * @return boolean
     */
    public function hasLongName()
    {
        return (null !== $this->longName);
    }

    /**
     * Determine whether the option is required
     *
     * @return boolean
     */
    public function isRequired()
    {
        return $this->required;
    }

}