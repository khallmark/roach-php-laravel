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

namespace RoachPHP\Laravel\Resolver;

use RoachPHP\Shell\Resolver\NamespaceResolverInterface;
use RoachPHP\Spider\SpiderInterface;

/**
 * Resolves a spider name against any number of registered namespaces.
 */
final class CompositeNamespaceResolver implements NamespaceResolverInterface
{
    public function __construct(
        private readonly NamespaceResolverInterface $inner,
        private readonly SpiderNamespaceRegistry $namespaces,
    ) {
    }

    public function resolveSpiderNamespace(string $spiderClass): string
    {
        $spiderClass = \mb_trim($spiderClass);

        if ($this->shouldResolveAsGiven($spiderClass)) {
            return $this->inner->resolveSpiderNamespace($spiderClass);
        }

        $fallback = $spiderClass;

        foreach ($this->namespaces->all() as $namespace) {
            $candidate = $namespace . '\\' . $spiderClass;
            $fallback = $candidate;

            if (\class_exists($candidate) && \is_a($candidate, SpiderInterface::class, true)) {
                return $this->inner->resolveSpiderNamespace($candidate);
            }
        }

        return $this->inner->resolveSpiderNamespace($fallback);
    }

    private function shouldResolveAsGiven(string $spiderClass): bool
    {
        if (\str_starts_with($spiderClass, '\\') || \class_exists($spiderClass)) {
            return true;
        }

        foreach ($this->namespaces->all() as $namespace) {
            if ($spiderClass === $namespace || \str_starts_with($spiderClass, $namespace . '\\')) {
                return true;
            }
        }

        return false;
    }
}
