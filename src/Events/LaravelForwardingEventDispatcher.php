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

namespace RoachPHP\Laravel\Events;

use Illuminate\Contracts\Events\Dispatcher as LaravelDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Symfony dispatcher that also re-emits Roach core events on Laravel's event bus.
 */
final class LaravelForwardingEventDispatcher extends EventDispatcher
{
    private const ROACH_EVENT_NAMESPACE = 'RoachPHP\\Events\\';

    public function __construct(
        private readonly LaravelDispatcher $laravel,
        private readonly bool $enabled = true,
    ) {
        parent::__construct();
    }

    public function dispatch(object $event, ?string $eventName = null): object
    {
        $event = parent::dispatch($event, $eventName);

        if ($this->enabled && \str_starts_with($event::class, self::ROACH_EVENT_NAMESPACE)) {
            $this->laravel->dispatch($event);
        }

        return $event;
    }
}
