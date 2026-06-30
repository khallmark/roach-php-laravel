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

use RoachPHP\Http\Client;
use RoachPHP\Scheduling\ArrayRequestScheduler;

return [
    /*
    |--------------------------------------------------------------------------
    | Request Queue
    |--------------------------------------------------------------------------
    |
    | The RequestQueue implementation Roach uses to schedule new requests
    | during a run.
    |
    | Needs to implement the RoachPHP\Scheduling\RequestScheduler interface.
    |
     */
    'request_queue' => ArrayRequestScheduler::class,
    /*
    |--------------------------------------------------------------------------
    | HTTP Client
    |--------------------------------------------------------------------------
    |
    | The HTTP client implementation Roach uses to dispatch new request
    | during a run.
    |
    | Needs to implement the RoachPHP\Http\ClientInterface interface.
    |
     */
    'client' => Client::class,
    /*
    |--------------------------------------------------------------------------
    | Default Spider Namespace
    |--------------------------------------------------------------------------
    |
    | Legacy default namespace used as the final short-name fallback. This key
    | is still honored for backward compatibility.
    |
     */
    'default_spider_namespace' => 'App\Spiders',
    /*
    |--------------------------------------------------------------------------
    | Additional Spider Namespaces
    |--------------------------------------------------------------------------
    |
    | The roach:run command tries each namespace, in order, when resolving a
    | short spider name. Package service providers may also register namespaces
    | at runtime with SpiderNamespaceRegistry::register().
    |
    | @var list<string>
    |
     */
    'spider_namespaces' => [
        // 'App\Spiders',
    ],
    /*
    |--------------------------------------------------------------------------
    | Laravel Event Bridge
    |--------------------------------------------------------------------------
    |
    | Re-emit Roach core events (RoachPHP\Events\*) onto Laravel's event
    | dispatcher so applications can use Laravel listeners, queued listeners,
    | and broadcasting. Set false for stock Symfony-only behavior.
    |
     */
    'bridge_events' => env('ROACH_BRIDGE_EVENTS', true),
];
