<?php
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

use Pop\Application;
use Pop\Console\Console;

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
     * Get application object (alias method)
     *
     * @return ?Application
     */
    public function application(): ?Application;

    /**
     * Get console object (alias method)
     *
     * @return ?Console
     */
    public function console(): ?Console;

    /**
     * Set the command application
     *
     * @param  Application $application
     * @return static
     */
    public function setApplication(Application $application): static;

    /**
     * Set the command console
     *
     * @param  Console $console
     * @return static
     */
    public function setConsole(Console $console): static;

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
     * Get application object
     *
     * @return ?Application
     */
    public function getApplication(): ?Application;

    /**
     * Get console object
     *
     * @return ?Console
     */
    public function getConsole(): ?Console;

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
     * Determine if the command has an application
     *
     * @return bool
     */
    public function hasApplication(): bool;

    /**
     * Determine if the command has a console
     *
     * @return bool
     */
    public function hasConsole(): bool;

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

}
