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

namespace RoachPHP\Laravel\Tests\Fixtures\Resolver\Secondary;

use RoachPHP\Http\Response;
use RoachPHP\Spider\BasicSpider;
use RoachPHP\Spider\ParseResult;

/**
 * @internal
 */
final class SharedSpider extends BasicSpider
{
    /**
     * @return \Generator<int, ParseResult>
     */
    public function parse(Response $response): \Generator
    {
        yield from [];
    }
}
