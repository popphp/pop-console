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
namespace Pop\Console\Command;

/**
 * Console command interface
 *
 * @category   Pop
 * @package    Pop\Console
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2027 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.0
 */
interface CommandInterface
{

    /**
     * Set the command name
     *
     * @param  string $name
     * @return static
     */
    public function setName(string $name): static;

    /**
     * Set the command params
     *
     * @param  string $params
     * @return static
     */
    public function setParams(string $params): static;

    /**
     * Set the command help
     *
     * @param  string $help
     * @return static
     */
    public function setHelp(string $help): static;

    /**
     * Set the script name the command was registered under
     *
     * @param  string $scriptName
     * @return static
     */
    public function setScriptName(string $scriptName): static;

    /**
     * Get the command name
     *
     * @return ?string
     */
    public function getName(): ?string;

    /**
     * Get the command params
     *
     * @return ?string
     */
    public function getParams(): ?string;

    /**
     * Get the command help
     *
     * @return ?string
     */
    public function getHelp(): ?string;

    /**
     * Get the script name the command was registered under
     *
     * @return ?string
     */
    public function getScriptName(): ?string;

    /**
     * Determine if the command has name
     *
     * @return bool
     */
    public function hasName(): bool;

    /**
     * Determine if the command has params
     *
     * @return bool
     */
    public function hasParams(): bool;

    /**
     * Determine if the command has help
     *
     * @return bool
     */
    public function hasHelp(): bool;

    /**
     * Determine if the command has a script name
     *
     * @return bool
     */
    public function hasScriptName(): bool;

    /**
     * Render the command as its name and params
     *
     * @return string
     */
    public function __toString(): string;

}
