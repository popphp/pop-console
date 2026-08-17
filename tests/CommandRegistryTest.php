<?php

namespace Pop\Console\Test;

use Pop\Application;
use Pop\Console\Command;
use Pop\Console\Command\AbstractCommand;
use Pop\Console\CommandRegistry;
use PHPUnit\Framework\TestCase;

class CommandRegistryTest extends TestCase
{

    public function testAddAndGet()
    {
        $registry = new CommandRegistry();
        $registry->add(new Command(name: 'help'));

        $this->assertTrue($registry->has('help'));
        $this->assertEquals('help', $registry->get('help')->getName());
    }

    public function testGetReturnsNullWhenNotFound()
    {
        $registry = new CommandRegistry();
        $this->assertNull($registry->get('missing'));
        $this->assertFalse($registry->has('missing'));
    }

    public function testAddAllAndAll()
    {
        $registry = new CommandRegistry();
        $registry->add(new Command(name: 'help'));
        $registry->addAll([
            new Command(name: 'list'),
            new Command(name: 'print')
        ]);

        $this->assertCount(3, $registry->all());
        $this->assertEquals('list', $registry->get('list')->getName());
        $this->assertEquals('print', $registry->get('print')->getName());
    }

    public function testAddOverwritesExistingCommandWithSameName()
    {
        $registry = new CommandRegistry();
        $registry->add(new Command(name: 'help', help: 'First'));
        $registry->add(new Command(name: 'help', help: 'Second'));

        $this->assertCount(1, $registry->all());
        $this->assertEquals('Second', $registry->get('help')->getHelp());
    }

    public function testFromRoutes()
    {
        $app = new Application(['routes' => [
            'app:init [--web] [--api] [--cli] <namespace>' => [
                'controller' => 'MyAppController',
                'action'     => 'init',
                'help'       => 'Init application' . PHP_EOL
            ],
            'db:config' => [
                'controller' => 'MyAppController',
                'action'     => 'config',
                'help'       => 'Config DB'
            ]
        ]]);

        $registry = new CommandRegistry();
        $commands = $registry->fromRoutes($app->router()->getRouteMatch(), './app');

        $this->assertCount(2, $commands);
        $this->assertCount(0, $registry->all());
        $this->assertEquals('./app app:init', $commands[0]->getName());
        $this->assertEquals('./app db:config', $commands[1]->getName());
        $this->assertEquals('./app', $commands[0]->getScriptName());
        $this->assertEquals('./app', $commands[1]->getScriptName());
    }

    public function testFromRoutesWithoutScriptNameLeavesScriptNameUnset()
    {
        $app = new Application(['routes' => [
            'db:config' => [
                'controller' => 'MyAppController',
                'action'     => 'config',
                'help'       => 'Config DB'
            ]
        ]]);

        $registry = new CommandRegistry();
        $commands = $registry->fromRoutes($app->router()->getRouteMatch());

        $this->assertCount(1, $commands);
        $this->assertEquals('db:config', $commands[0]->getName());
        $this->assertFalse($commands[0]->hasScriptName());
        $this->assertNull($commands[0]->getScriptName());
    }

    public function testFromRoutesGetsHelpFromCommandObjectWhenRouteHasNoHelp()
    {
        $app = new Application(['routes' => [
            'app:init [--web] [--api] [--cli] <namespace>' => [
                'controller' => CommandRegistryTestCommand::class,
                'action'     => 'init'
            ]
        ]]);

        $registry = new CommandRegistry();
        $commands = $registry->fromRoutes($app->router()->getRouteMatch(), './app');

        $this->assertCount(1, $commands);
        $this->assertEquals('Help from command object', $commands[0]->getHelp());
    }

    public function testLoadRoutesBuildsRoutesFromCommandClassesInDirectory()
    {
        $routes = CommandRegistry::loadRoutes([], __DIR__ . '/Fixtures/Commands');

        $this->assertCount(2, $routes);

        $this->assertArrayHasKey('foo:bar <id>', $routes);
        $this->assertEquals(
            'Pop\Console\Test\Fixtures\Commands\FooCommand', $routes['foo:bar <id>']['controller']
        );

        $this->assertArrayHasKey('baz:qux', $routes);
        $this->assertEquals(
            'Pop\Console\Test\Fixtures\Commands\BazCommand', $routes['baz:qux']['controller']
        );
    }

    public function testLoadRoutesGivesExistingRoutesPrecedenceOverAutoDiscovered()
    {
        $routes = CommandRegistry::loadRoutes([
            'foo:bar <id>' => ['controller' => 'Custom\Controller']
        ], __DIR__ . '/Fixtures/Commands');

        $this->assertEquals('Custom\Controller', $routes['foo:bar <id>']['controller']);
    }

    public function testLoadRoutesMergesWithExistingRoutesUnderDifferentKeys()
    {
        $routes = CommandRegistry::loadRoutes([
            'existing:route' => ['controller' => 'Existing\Controller']
        ], __DIR__ . '/Fixtures/Commands');

        $this->assertCount(3, $routes);
        $this->assertArrayHasKey('existing:route', $routes);
        $this->assertArrayHasKey('foo:bar <id>', $routes);
        $this->assertArrayHasKey('baz:qux', $routes);
    }

    public function testLoadRoutesReturnsRoutesUnchangedWhenLocationDoesNotExist()
    {
        $routes = ['existing:route' => ['controller' => 'Existing\Controller']];
        $result = CommandRegistry::loadRoutes($routes, __DIR__ . '/Fixtures/DoesNotExist');

        $this->assertSame($routes, $result);
    }

    public function testAddFromRoutes()
    {
        $app = new Application(['routes' => [
            'app:init [--web] [--api] [--cli] <namespace>' => [
                'controller' => 'MyAppController',
                'action'     => 'init',
                'help'       => 'Init application' . PHP_EOL
            ],
            'db:config' => [
                'controller' => 'MyAppController',
                'action'     => 'config',
                'help'       => 'Config DB'
            ]
        ]]);

        $registry = new CommandRegistry();
        $registry->addFromRoutes($app->router()->getRouteMatch(), './app');

        $this->assertTrue($registry->has('./app app:init'));
        $this->assertTrue($registry->has('./app db:config'));
    }

}

class CommandRegistryTestCommand extends AbstractCommand
{

    public function getHelp(): ?string
    {
        return 'Help from command object';
    }

}
