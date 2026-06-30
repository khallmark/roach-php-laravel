<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Kai Sassnowski
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/roach-php/laravel
 */

namespace RoachPHP\Laravel;

use Illuminate\Contracts\Events\Dispatcher as LaravelDispatcher;
use Illuminate\Foundation\Application;
use RoachPHP\Core\Engine;
use RoachPHP\Core\EngineInterface;
use RoachPHP\Core\Runner;
use RoachPHP\Core\RunnerInterface;
use RoachPHP\Http\ClientInterface;
use RoachPHP\ItemPipeline\ItemPipeline;
use RoachPHP\ItemPipeline\ItemPipelineInterface;
use RoachPHP\Laravel\Commands\SpiderMakeCommand;
use RoachPHP\Laravel\Events\LaravelForwardingEventDispatcher;
use RoachPHP\Laravel\Resolver\CompositeNamespaceResolver;
use RoachPHP\Laravel\Resolver\SpiderNamespaceRegistry;
use RoachPHP\Roach;
use RoachPHP\Scheduling\RequestSchedulerInterface;
use RoachPHP\Scheduling\Timing\ClockInterface;
use RoachPHP\Scheduling\Timing\SystemClock;
use RoachPHP\Shell\Commands\RunSpiderCommand;
use RoachPHP\Shell\Repl;
use RoachPHP\Shell\Resolver\NamespaceResolverInterface;
use RoachPHP\Shell\Resolver\StaticNamespaceResolver;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class RoachServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('roach')
            ->hasConfigFile()
            ->hasCommands([
                SpiderMakeCommand::class,
                Repl::class,
                RunSpiderCommand::class,
            ]);
    }

    public function registeringPackage(): void
    {
        $this->app->bind(EngineInterface::class, Engine::class);
        $this->app->bind(RunnerInterface::class, Runner::class);
        $this->app->singleton(EventDispatcher::class, static function (Application $app): EventDispatcher {
            if (!(bool) config('roach.bridge_events', true)) {
                return new EventDispatcher();
            }

            return new LaravelForwardingEventDispatcher($app->make(LaravelDispatcher::class));
        });
        $this->app->singleton(
            EventDispatcherInterface::class,
            static fn (Application $app): EventDispatcher => $app->make(EventDispatcher::class),
        );
        $this->app->bind(
            ClientInterface::class,
            static fn (Application $app) => $app->make(config('roach.client')),
        );
        $this->app->singleton(
            RequestSchedulerInterface::class,
            static fn (Application $app) => $app->make(config('roach.request_queue')),
        );
        $this->app->bind(ClockInterface::class, SystemClock::class);
        $this->app->bind(ItemPipelineInterface::class, ItemPipeline::class);
        $this->app->singleton(SpiderNamespaceRegistry::class, static function (): SpiderNamespaceRegistry {
            $configuredNamespaces = config('roach.spider_namespaces') ?: [];

            if (!\is_iterable($configuredNamespaces)) {
                $configuredNamespaces = [$configuredNamespaces];
            }

            $namespaces = [];

            foreach ($configuredNamespaces as $namespace) {
                if (\is_string($namespace)) {
                    $namespaces[] = $namespace;
                }
            }

            $defaultNamespace = config('roach.default_spider_namespace') ?: 'App\\Spiders';
            $namespaces[] = \is_string($defaultNamespace) ? $defaultNamespace : 'App\\Spiders';

            return new SpiderNamespaceRegistry($namespaces);
        });
        $this->app->bind(
            NamespaceResolverInterface::class,
            static fn (Application $app): CompositeNamespaceResolver => new CompositeNamespaceResolver(
                new StaticNamespaceResolver(),
                $app->make(SpiderNamespaceRegistry::class),
            ),
        );
    }

    public function bootingPackage(): void
    {
        Roach::useContainer($this->app);
    }
}
