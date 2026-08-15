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
     * Traits
     */
    use Dispatch\ConsoleTrait {
        Dispatch\ConsoleTrait::__construct as private traitConstruct;
    }

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
     * @param ?Application $application
     * @param Console      $console
     * @param ?string      $name
     * @param ?string      $params
     * @param ?string      $help
     */
    public function __construct(
        ?Application $application = null, Console $console = new Console(120),
        ?string $name = null, ?string $params = null, ?string $help = null
    )
    {
        $this->traitConstruct($application, $console);

        if ($name !== null) {
            $this->setName($name);
        }
        if ($params !== null) {
            $this->setParams($params);
        }
        if ($help !== null) {
            $this->setHelp($help);
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
