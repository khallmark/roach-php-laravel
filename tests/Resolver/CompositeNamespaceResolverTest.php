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

namespace RoachPHP\Laravel\Tests\Resolver;

use RoachPHP\Laravel\Resolver\CompositeNamespaceResolver;
use RoachPHP\Laravel\Resolver\SpiderNamespaceRegistry;
use RoachPHP\Laravel\Tests\Fixtures\Resolver\Primary;
use RoachPHP\Laravel\Tests\Fixtures\Resolver\Secondary;
use RoachPHP\Laravel\Tests\TestCase;
use RoachPHP\Shell\InvalidSpiderException;
use RoachPHP\Shell\Resolver\StaticNamespaceResolver;

/**
 * @internal
 */
final class CompositeNamespaceResolverTest extends TestCase
{
    private const PRIMARY_NAMESPACE = 'RoachPHP\Laravel\Tests\Fixtures\Resolver\Primary';

    private const SECONDARY_NAMESPACE = 'RoachPHP\Laravel\Tests\Fixtures\Resolver\Secondary';

    public function testExistingFullyQualifiedClassNameReturnsUnchanged(): void
    {
        $resolver = self::resolver(self::PRIMARY_NAMESPACE);

        self::assertSame(
            Primary\ExampleSpider::class,
            $resolver->resolveSpiderNamespace(Primary\ExampleSpider::class),
        );
    }

    public function testShortNameResolvesAgainstSingleSeededNamespace(): void
    {
        $resolver = self::resolver(self::PRIMARY_NAMESPACE);

        self::assertSame(
            Primary\ExampleSpider::class,
            $resolver->resolveSpiderNamespace('ExampleSpider'),
        );
    }

    public function testShortNameResolvesAfterRuntimeRegistration(): void
    {
        $registry = new SpiderNamespaceRegistry([self::PRIMARY_NAMESPACE]);
        $resolver = new CompositeNamespaceResolver(new StaticNamespaceResolver(), $registry);

        $registry->register(self::SECONDARY_NAMESPACE);

        self::assertSame(
            Secondary\RuntimeSpider::class,
            $resolver->resolveSpiderNamespace('RuntimeSpider'),
        );
    }

    public function testFirstExistingSpiderWins(): void
    {
        $resolver = self::resolver(self::PRIMARY_NAMESPACE, self::SECONDARY_NAMESPACE);

        self::assertSame(
            Primary\SharedSpider::class,
            $resolver->resolveSpiderNamespace('SharedSpider'),
        );
    }

    public function testEarlierExistingNonSpiderClassIsIgnored(): void
    {
        $resolver = self::resolver(self::PRIMARY_NAMESPACE, self::SECONDARY_NAMESPACE);

        self::assertSame(
            Secondary\AmbiguousSpider::class,
            $resolver->resolveSpiderNamespace('AmbiguousSpider'),
        );
    }

    public function testMissingShortNameFallsBackToFinalNamespacedCandidate(): void
    {
        $resolver = self::resolver(self::PRIMARY_NAMESPACE);

        $this->expectException(InvalidSpiderException::class);
        $this->expectExceptionMessage('The spider class ' . self::PRIMARY_NAMESPACE . '\MissingSpider does not exist');

        $resolver->resolveSpiderNamespace('MissingSpider');
    }

    public function testAlreadyNamespacedMissingClassUnderRegisteredNamespaceIsNotDoublePrefixed(): void
    {
        $resolver = self::resolver(self::PRIMARY_NAMESPACE);

        $missingSpider = self::PRIMARY_NAMESPACE . '\MissingSpider';

        $this->expectException(InvalidSpiderException::class);
        $this->expectExceptionMessage("The spider class {$missingSpider} does not exist");

        $resolver->resolveSpiderNamespace($missingSpider);
    }

    private static function resolver(string ...$namespaces): CompositeNamespaceResolver
    {
        return new CompositeNamespaceResolver(
            new StaticNamespaceResolver(),
            new SpiderNamespaceRegistry($namespaces),
        );
    }
}
