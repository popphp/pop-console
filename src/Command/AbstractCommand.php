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
use Pop\Dispatch;
use Pop\Console\Console;

/**
 * Console abstract command class
 *
 * @category   Pop
 * @package    Pop\Console
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2027 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.0
 */
abstract class AbstractCommand extends Dispatch\AbstractDispatcher implements Dispatch\DispatchableInterface, CommandInterface
{

    /**
     * Application object
     * @var ?Application
     */
    protected ?Application $application = null;

    /**
     * Console object
     * @var ?Console
     */
    protected ?Console $console = null;

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
     * @param ?string      $name
     * @param ?string      $params
     * @param ?string      $help
     * @param ?Application $application
     * @param ?Console     $console
     */
    public function __construct(
        ?string $name = null, ?string $params = null, ?string $help = null,
        ?Application $application = null, ?Console $console = null
    )
    {
        if ($name !== null) {
            $this->setName($name);
        }
        if ($params !== null) {
            $this->setParams($params);
        }
        if ($help !== null) {
            $this->setHelp($help);
        }
        if ($application !== null) {
            $this->setApplication($application);
        }
        if ($console !== null) {
            $this->setConsole($console);
        }
    }

    /**
     * Dispatch the command
     *
     * Resolves to the 'handle' action when none is given, always passing it through
     * as an explicit action name rather than relying on AbstractDispatcher's own
     * default-action fallback, which does not forward $params.
     *
     * @param  ?string $action
     * @param  ?array  $params
     * @return void
     */
    public function dispatch(?string $action = null, ?array $params = null): void
    {
        parent::dispatch($action ?? 'handle', $params);
    }

    /**
     * Load the command
     *
     * @param  ?string $name
     * @param  array   $config
     * @return static
     */
    public static function load(?string $name = null, array $config = []): static
    {
        $params      = $config['params'] ?? null;
        $help        = $config['help'] ?? null;
        $application = $config['application'] ?? null;
        $console     = $config['console'] ?? null;

        return new static($name, $params, $help, $application, $console);
    }

    /**
     * Load the command for an application
     *
     * @param  ?Application $application
     * @param  ?Console     $console
     * @param  ?string      $name
     * @param  array        $config
     * @return static
     */
    public static function loadForApplication(
        ?Application $application = null, ?Console $console = null, ?string $name = null, array $config = []
    ): static
    {
        $params     = $config['params'] ?? null;
        $help       = $config['help'] ?? null;

        return new static($name, $params, $help, $application, $console);
    }

    /**
     * Get application object (alias method)
     *
     * @return ?Application
     */
    public function application(): ?Application
    {
        return $this->application;
    }

    /**
     * Get console object (alias method)
     *
     * @return ?Console
     */
    public function console(): ?Console
    {
        return $this->console;
    }

    /**
     * Set the command application
     *
     * @param  Application $application
     * @return static
     */
    public function setApplication(Application $application): static
    {
        $this->application = $application;
        return $this;
    }

    /**
     * Set the command console
     *
     * @param  Console $console
     * @return static
     */
    public function setConsole(Console $console): static
    {
        $this->console = $console;
        return $this;
    }

    /**
     * Set the command name
     *
     * @param  string $name
     * @return static
     */
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Set the command params
     *
     * @param  string $params
     * @return static
     */
    public function setParams(string $params): static
    {
        $this->params = $params;
        return $this;
    }

    /**
     * Set the command help
     *
     * @param  string $help
     * @return static
     */
    public function setHelp(string $help): static
    {
        $this->help = $help;
        return $this;
    }

    /**
     * Get application object
     *
     * @return ?Application
     */
    public function getApplication(): ?Application
    {
        return $this->application;
    }

    /**
     * Get console object
     *
     * @return ?Console
     */
    public function getConsole(): ?Console
    {
        return $this->console;
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
     * Get the command help
     *
     * @return ?string
     */
    public function getHelp(): ?string
    {
        return $this->help;
    }

    /**
     * Determine if the command has an application
     *
     * @return bool
     */
    public function hasApplication(): bool
    {
        return ($this->application !== null);
    }

    /**
     * Determine if the command has a console
     *
     * @return bool
     */
    public function hasConsole(): bool
    {
        return ($this->console !== null);
    }

    /**
     * Determine if the command has name
     *
     * @return bool
     */
    public function hasName(): bool
    {
        return ($this->name !== null);
    }

    /**
     * Determine if the command has params
     *
     * @return bool
     */
    public function hasParams(): bool
    {
        return ($this->params !== null);
    }

    /**
     * Determine if the command has help
     *
     * @return bool
     */
    public function hasHelp(): bool
    {
        return ($this->help !== null);
    }

    /**
     * Return the command name as string
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->name . (($this->params !== null) ? ' ' . $this->params : null);
    }

}
