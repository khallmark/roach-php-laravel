<?php

declare(strict_types=1);

/**
 * Copyright (c) 2026 Kevin Hallmark
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/roach-php/laravel
 */

namespace RoachPHP\Laravel\Tests;

use RoachPHP\Laravel\Events\LaravelForwardingEventDispatcher;
use RoachPHP\Laravel\Resolver\CompositeNamespaceResolver;
use RoachPHP\Laravel\Resolver\SpiderNamespaceRegistry;
use RoachPHP\Shell\Resolver\NamespaceResolverInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 */
final class RoachServiceProviderTest extends TestCase
{
    public function testNamespaceResolverBindingUsesCompositeResolver(): void
    {
        self::assertInstanceOf(
            CompositeNamespaceResolver::class,
            app(NamespaceResolverInterface::class),
        );
    }

    public function testEventDispatcherBindingsResolveToSameSingleton(): void
    {
        self::assertSame(
            app(EventDispatcher::class),
            app(EventDispatcherInterface::class),
        );
    }

    public function testBridgeEnabledUsesForwardingDispatcher(): void
    {
        self::assertInstanceOf(
            LaravelForwardingEventDispatcher::class,
            app(EventDispatcher::class),
        );
    }

    public function testSpiderNamespaceRegistryIsSeededFromConfigAndDefaultLast(): void
    {
        config([
            'roach.spider_namespaces' => ['Package\First', 'Package\Second', 'App\Spiders'],
            'roach.default_spider_namespace' => 'App\Spiders',
        ]);

        self::assertSame(
            ['Package\First', 'Package\Second', 'App\Spiders'],
            app(SpiderNamespaceRegistry::class)->all(),
        );
    }
}
