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

namespace RoachPHP\Laravel\Tests\Events;

use Illuminate\Support\Facades\Event as LaravelEvent;
use RoachPHP\Events\ItemScraped;
use RoachPHP\ItemPipeline\Item;
use RoachPHP\Laravel\Tests\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\Event as SymfonyEvent;

/**
 * @internal
 */
final class LaravelForwardingEventDispatcherTest extends TestCase
{
    public function testRoachEventsAreForwardedToLaravel(): void
    {
        LaravelEvent::fake([ItemScraped::class]);

        $dispatcher = app(EventDispatcher::class);
        $event = new ItemScraped(new Item(['id' => 1]));

        $dispatcher->dispatch($event, ItemScraped::NAME);

        LaravelEvent::assertDispatched(
            ItemScraped::class,
            static fn (ItemScraped $forwarded): bool => $forwarded === $event,
        );
    }

    public function testNonRoachSymfonyEventsAreNotForwardedToLaravel(): void
    {
        LaravelEvent::fake();

        $dispatcher = app(EventDispatcher::class);
        $event = new class() extends SymfonyEvent {
        };

        $dispatcher->dispatch($event, 'non.roach');

        LaravelEvent::assertNotDispatched($event::class);
    }

    public function testBridgeCanBeDisabled(): void
    {
        config(['roach.bridge_events' => false]);
        LaravelEvent::fake([ItemScraped::class]);

        $dispatcher = app(EventDispatcher::class);

        self::assertSame(EventDispatcher::class, $dispatcher::class);

        $dispatcher->dispatch(new ItemScraped(new Item(['id' => 1])), ItemScraped::NAME);

        LaravelEvent::assertNotDispatched(ItemScraped::class);
    }

    public function testSymfonySubscribersStillReceiveRoachEvents(): void
    {
        $subscriber = new class() implements EventSubscriberInterface {
            public ?ItemScraped $received = null;

            /**
             * @return array<string, string>
             */
            public static function getSubscribedEvents(): array
            {
                return [ItemScraped::NAME => 'handle'];
            }

            public function handle(ItemScraped $event): void
            {
                $this->received = $event;
            }
        };

        $dispatcher = app(EventDispatcher::class);
        $dispatcher->addSubscriber($subscriber);

        $event = new ItemScraped(new Item(['id' => 1]));
        $dispatcher->dispatch($event, ItemScraped::NAME);

        self::assertSame($event, $subscriber->received);
    }
}
