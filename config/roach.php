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
    | The default namespace the `roach:run` and `roach:spider` commands use
    | to determine the namespace of spider classes. This should not contain
    | leading or trailing backslashes.
    |
     */
    'default_spider_namespace' => 'App\Spiders',
];
