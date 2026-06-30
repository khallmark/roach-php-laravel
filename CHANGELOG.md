# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [4.0.0] - 2026-06-30

### Added

- Added multi-namespace spider resolution for `roach:run` through `roach.spider_namespaces` and `RoachPHP\Laravel\Resolver\SpiderNamespaceRegistry::register()`.
- Added a Symfony-to-Laravel event bridge that re-emits Roach core events (`RoachPHP\Events\*`) on Laravel's event dispatcher. This enables Laravel listeners, queued listeners, and broadcasting. Configure with `roach.bridge_events` / `ROACH_BRIDGE_EVENTS`.

### Changed

- `RoachPHP\Shell\Resolver\NamespaceResolverInterface` now resolves to `RoachPHP\Laravel\Resolver\CompositeNamespaceResolver` in Laravel applications.
- `Symfony\Component\EventDispatcher\EventDispatcher::class` now resolves to `RoachPHP\Laravel\Events\LaravelForwardingEventDispatcher` when event bridging is enabled.
- `Symfony\Component\EventDispatcher\EventDispatcherInterface::class` and `EventDispatcher::class` now resolve to the same singleton instance.
- Requires `roach-php/core:^4.0`.

### Backward compatibility

- `default_spider_namespace` is still honored and seeded as the final fallback namespace.
- Programmatic `Roach::startSpider()` and `Roach::collectSpider()` calls are unchanged.
- Code binding against concrete `DefaultNamespaceResolverDecorator` should bind against `NamespaceResolverInterface` instead.
- Code asserting `get_class($dispatcher) === EventDispatcher::class` should use `instanceof EventDispatcher` or disable forwarding with `ROACH_BRIDGE_EVENTS=false`.

## [3.2.0] - 2025-03-21

### Added

- Added support for Laravel 12
- Added support for PHP 8.4

## [3.1.0] – 2024-03-22

### Added

- Added support for Laravel 11

## [3.0.0] – 2024-01-05

### Added

- Added support for PHP 8.3

### Removed

- Dropped Laravel 9 support

## [2.0.0] – 2022-02-06

### Added

- Added support for Laravel 10

### Removed

- Dropped support for PHP 8.0

## [1.0.0] - 2022-04-19

### Added

- Added support for `roach-php/core:^1.0`
- Added `force (-f)` optioni to `roach:spider` command (@josezenem, #9)
- Added `default_spider_namespace` configuration option. This option is used by both the
  `roach:spider` and `roach:run` commands

### Fixed

- Fixed bug where `artisan roach:run` command would sometimes not correctly parse namespaces (fixes [#11](https://github.com/roach-php/laravel/issues/11))

### Removed

- Drop support for Laravel 8

## [0.3.1] - 2022-02-01

### Fixed

- Fixed default container bindings not being registered correctly if package config has not been published (fixes #5)

## [0.3.0] - 2022-01-30

### Added

- Added support for Laravel 9

## [0.2.1] - 2022-01-04

### Fixed

- Update incorrect namespace in spider stub (#4). Credits @RyanPriceDotCa

## [0.2.0] - 2021-12-28

### Changed

- Bump `roach-php/core` dependency to `^0.2.0` which includes a middleware to execute
  Javascript on a page.

## [0.1.0] - 2021-12-27

### Added

- Initial release

[3.2.0]: https://github.com/roach-php/laravel/compare/3.1.0...3.2.0
[3.1.0]: https://github.com/roach-php/laravel/compare/3.0.0...3.1.0
[3.0.0]: https://github.com/roach-php/laravel/compare/2.0.0...3.0.0
[2.0.0]: https://github.com/roach-php/laravel/compare/1.0.0...2.0.0
[1.0.0]: https://github.com/roach-php/laravel/compare/0.3.1...1.0.0
[0.3.1]: https://github.com/roach-php/laravel/compare/0.3.0...0.3.1
[0.3.0]: https://github.com/roach-php/laravel/compare/0.2.1...0.3.0
[0.2.1]: https://github.com/roach-php/laravel/compare/0.2.0...0.2.1
[0.2.0]: https://github.com/roach-php/laravel/compare/0.1.0...0.2.0
