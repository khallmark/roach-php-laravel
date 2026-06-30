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

/**
 * Ordered, de-duplicated set of namespaces that may contain spider classes.
 */
final class SpiderNamespaceRegistry
{
    /**
     * @var list<string>
     */
    private array $namespaces = [];

    /**
     * @param iterable<string> $namespaces
     */
    public function __construct(iterable $namespaces = [])
    {
        foreach ($namespaces as $namespace) {
            $this->register($namespace);
        }
    }

    public function register(string $namespace): void
    {
        $namespace = \mb_trim($namespace, " \t\n\r\0\x0B\\");

        if ('' === $namespace || \in_array($namespace, $this->namespaces, true)) {
            return;
        }

        $this->namespaces[] = $namespace;
    }

    /**
     * @return list<string>
     */
    public function all(): array
    {
        return $this->namespaces;
    }
}
