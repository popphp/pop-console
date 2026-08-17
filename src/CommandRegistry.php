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
namespace Pop\Console;

use Pop\Router\Match\Cli;

/**
 * Console command registry class
 *
 * @category   Pop
 * @package    Pop\Console
 * @author     Nick Sagona, III <dev@noladev.com>
 * @copyright  Copyright (c) 2009-2027 NOLA Interactive, LLC.
 * @license    https://www.popphp.org/license     New BSD License
 * @version    5.0.0
 */
class CommandRegistry
{

    /**
     * Commands
     * @var array
     */
    protected array $commands = [];

    /**
     * Add a command
     *
     * @param  Command $command
     * @return static
     */
    public function add(Command $command): static
    {
        $this->commands[$command->getName()] = $command;
        return $this;
    }

    /**
     * Add commands
     *
     * @param  array $commands
     * @return static
     */
    public function addAll(array $commands): static
    {
        foreach ($commands as $command) {
            $this->add($command);
        }
        return $this;
    }

    /**
     * Get all commands
     *
     * @return array
     */
    public function all(): array
    {
        return $this->commands;
    }

    /**
     * Get a command
     *
     * @param  string $name
     * @return Command|null
     */
    public function get(string $name): Command|null
    {
        return $this->commands[$name] ?? null;
    }

    /**
     * Check if the registry has a command
     *
     * @param  string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->commands[$name]);
    }

    /**
     * Build commands from routes
     *
     * @param  Cli     $routeMatch
     * @param  ?string $scriptName
     * @return array
     */
    public function fromRoutes(Cli $routeMatch, ?string $scriptName = null): array
    {
        $routeMatch->match();

        $commandRoutes = $routeMatch->getRoutes();
        $commands      = $routeMatch->getCommands();
        $commandsToAdd = [];

        foreach ($commands as $name => $command) {
            $commandName = implode(' ', $command);
            $params      = trim(substr((string)$name, strlen((string)$commandName)));
            $params      = (!empty($params)) ? $params : null;
            $help        = null;

            // Get help text directly from the route config
            if (isset($commandRoutes[$name]) && isset($commandRoutes[$name]['help'])) {
                $help = $commandRoutes[$name]['help'];
            // Else, get help from the command object help
            } else if (is_subclass_of($commandRoutes[$name]['controller'], 'Pop\Console\Command\CommandInterface', true)) {
                $commandClass = $commandRoutes[$name]['controller'];
                $help         = (new $commandClass())->getHelp();
            }

            if ($scriptName !== null) {
                $commandName = $scriptName . ' ' . $commandName;
            }

            $commandToAdd = new Command(name: $commandName, params: $params, help: $help);
            if ($scriptName !== null) {
                $commandToAdd->setScriptName($scriptName);
            }

            $commandsToAdd[] = $commandToAdd;
        }

        return $commandsToAdd;
    }

    /**
     * Add commands from routes
     *
     * @param  Cli     $routeMatch
     * @param  ?string $scriptName
     * @return static
     */
    public function addFromRoutes(Cli $routeMatch, ?string $scriptName = null): static
    {
        $commands = $this->fromRoutes($routeMatch, $scriptName);

        if (!empty($commands)) {
            $this->addAll($commands);
        }

        return $this;
    }

    /**
     * Load application commands into the route config array
     *
     * @param  array  $routes
     * @param  string $location
     * @param  bool   $prepend
     * @return array
     */
    public static function loadRoutes(array $routes, string $location, bool $prepend = true): array
    {
        if (!file_exists($location)) {
            return $routes;
        }

        $commands = array_values(array_filter(scandir($location), function ($value) {
            return (($value != '.') && ($value != '..') && ($value != '.empty'));
        }));

        $namespace     = null;
        $commandRoutes = [];
        $total         = count($commands);

        foreach ($commands as $i => $command) {
            $entry = self::buildCommandRouteEntry($command, $location, $namespace, $i == ($total - 1));
            if ($entry !== null) {
                [$key, $route]        = $entry;
                $commandRoutes[$key] = $route;
            }
        }

        if (!empty($commandRoutes)) {
            $routes = ($prepend) ? array_merge($commandRoutes, $routes) : array_merge($routes, $commandRoutes);
        }

        return $routes;
    }

    /**
     * Build a single route entry from a discovered command file
     *
     * @param  string  $command
     * @param  string  $location
     * @param  ?string $namespace
     * @param  bool    $isLast
     * @return ?array
     */
    protected static function buildCommandRouteEntry(string $command, string $location, ?string &$namespace, bool $isLast): ?array
    {
        if (!str_ends_with($command, '.php')) {
            return null;
        }

        if ($namespace === null) {
            $namespace = self::detectNamespace($location . DIRECTORY_SEPARATOR . $command);
        }

        $commandClass = $namespace . '\\' . substr($command, 0, -4);
        if (!class_exists($commandClass)) {
            return null;
        }

        $commandObject = new $commandClass();
        $commandRoute  = ['controller' => $commandClass];

        if ($commandObject->hasHelp()) {
            $commandRoute['help'] = $commandObject->getHelp() . ($isLast ? PHP_EOL : '');
        }

        return [(string)$commandObject, $commandRoute];
    }

    /**
     * Detect the namespace declared in a PHP file
     *
     * @param  string $file
     * @return ?string
     */
    protected static function detectNamespace(string $file): ?string
    {
        if (!file_exists($file)) {
            return null;
        }

        $matches = [];
        preg_match('/^\s*namespace\s+([^;]+);/m', file_get_contents($file), $matches);

        return isset($matches[1]) ? trim($matches[1]) : null;
    }

}
