<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp-framework
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
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
 * @copyright  Copyright (c) 2009-2024 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    4.1.0
 */
class Command
{

    /**
     * Command name
     * @var ?string
     */
    protected ?string $name = null;

    /**
     * Command params
     * @var ?string
     */
    protected ?string $params = null;

    /**
     * Command help
     * @var ?string
     */
    protected ?string $help = null;

    /**
     * Instantiate the command object
     *
     * @param  string  $name
     * @param  ?string $params
     * @param  ?string $help
     */
    public function __construct(string $name, ?string $params = null, ?string $help = null)
    {
        $this->setName($name);
        if ($params !== null) {
            $this->setParams($params);
        }
        if ($help !== null) {
            $this->setHelp($help);
        }
    }

    /**
     * Set the command name
     *
     * @param  string $name
     * @return Command
     */
    public function setName(string $name): Command
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
    public function setParams(string $params): Command
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
    public function setHelp(string $help): Command
    {
        $this->help = $help;
        return $this;
    }

    /**
     * Get the command name
     *
     * @return ?string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Get the command params
     *
     * @return ?string
     */
    public function getParams(): ?string
    {
        return $this->params;
    }

    /**
     * Determine if the command has params
     *
     * @return bool
     */
    public function hasParams(): bool
    {
        return (null !== $this->params);
    }

    /**
     * Get the command help
     *
     * @return ?string
     */
    public function getHelp(): ?string
    {
        return $this->help;
    }

    /**
     * Determine if the command has help
     *
     * @return bool
     */
    public function hasHelp(): bool
    {
        return (null !== $this->help);
    }

    /**
     * Return the command name as string
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->name . ((null !== $this->params) ? ' ' . $this->params : null);
    }

}
