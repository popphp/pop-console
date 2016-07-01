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
namespace Pop\Console\Input;

/**
 * Console input interface
 *
 * @category   Pop
 * @package    Pop_Console
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2016 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    2.1.0
 */
interface InputInterface
{

    /**
     * Constants for value mode
     */
    const VALUE_NONE     = 1;
    const VALUE_REQUIRED = 2;
    const VALUE_OPTIONAL = 3;
    const VALUE_IS_ARRAY = 4;

    /**
     * Set if the input value is required
     *
     * @param  boolean $optional
     * @return InputInterface
     */
    public function setValueOptional($optional);

    /**
     * Set if the input value is required
     *
     * @param  boolean $required
     * @return InputInterface
     */
    public function setValueRequired($required);

    /**
     * Set if the input value is an array
     *
     * @param  boolean $isArray
     * @return InputInterface
     */
    public function setValueIsArray($isArray);

    /**
     * Set the input value
     *
     * @param  mixed $value
     * @return InputInterface
     */
    public function setValue($value);

    /**
     * Determine whether the input accepts a value
     *
     * @return boolean
     */
    public function acceptsValue();

    /**
     * Determine whether the input value is optional
     *
     * @return boolean
     */
    public function isValueOptional();

    /**
     * Determine whether the input value is required
     *
     * @return boolean
     */
    public function isValueRequired();

    /**
     * Determine whether the input value is required
     *
     * @return boolean
     */
    public function isValueArray();

    /**
     * Get the input value
     *
     * @return mixed
     */
    public function getValue();

}