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
 * Console abstract input class
 *
 * @category   Pop
 * @package    Pop_Console
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2015 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    2.0.0a
 */
abstract class AbstractInput implements InputInterface
{

    /**
     * Input value optional
     * @var boolean
     */
    protected $valueOptional = null;

    /**
     * Input value required
     * @var boolean
     */
    protected $valueRequired = null;

    /**
     * Input value is array
     * @var boolean
     */
    protected $valueIsArray = null;

    /**
     * Input value
     * @var mixed
     */
    protected $value = null;

    /**
     * Set if the input value is required
     *
     * @param  string $valueMode
     * @return AbstractInput
     */
    public function setValueMode($valueMode)
    {
        switch ($valueMode) {
            case self::VALUE_REQUIRED:
                $this->setValueRequired(true);
                break;
            case self::VALUE_OPTIONAL:
                $this->setValueOptional(true);
                break;
            case self::VALUE_REQUIRED|self::VALUE_IS_ARRAY:
                $this->setValueRequired(true);
                $this->setValueIsArray(true);
                break;
            case self::VALUE_OPTIONAL|self::VALUE_IS_ARRAY:
                $this->setValueOptional(true);
                $this->setValueIsArray(true);
                break;

        }
        return $this;
    }

    /**
     * Set if the input value is required
     *
     * @param  boolean $optional
     * @return AbstractInput
     */
    public function setValueOptional($optional)
    {
        $this->valueOptional = (bool)$optional;
        return $this;
    }

    /**
     * Set if the input value is required
     *
     * @param  boolean $required
     * @return AbstractInput
     */
    public function setValueRequired($required)
    {
        $this->valueRequired = (bool)$required;
        return $this;
    }

    /**
     * Set if the input value is an array
     *
     * @param  boolean $isArray
     * @return AbstractInput
     */
    public function setValueIsArray($isArray)
    {
        $this->valueIsArray = (bool)$isArray;
        return $this;
    }

    /**
     * Set the input value
     *
     * @param  mixed $value
     * @return AbstractInput
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Determine whether the input accepts a value
     *
     * @return boolean
     */
    public function acceptsValue()
    {
        return (($this->valueOptional) || ($this->valueRequired));
    }

    /**
     * Determine whether the input value is optional
     *
     * @return boolean
     */
    public function isValueOptional()
    {
        return $this->valueOptional;
    }

    /**
     * Determine whether the input value is required
     *
     * @return boolean
     */
    public function isValueRequired()
    {
        return $this->valueRequired;
    }

    /**
     * Determine whether the input value is required
     *
     * @return boolean
     */
    public function isValueArray()
    {
        return $this->valueIsArray;
    }

    /**
     * Get the input value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

}