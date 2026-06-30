# Web Scraping for Laravel

This is the Laravel adapter for [Roach](https://roach-php.dev), the complete web scraping toolkit for PHP.

## Installation

Install the package via composer

```bash
composer require roach-php/laravel
```

## Documentation

Check out the [full documentation](https://roach-php.dev/docs/laravel) to get up and running.

## Multiple spider namespaces

Short spider names passed to `roach:run` are resolved against every configured namespace, in order.

```php
// config/roach.php
'spider_namespaces' => [
    'App\\Spiders',
    'Vendor\\Package\\Spiders',
],
```

Packages can register their namespace at runtime:

```php
use Illuminate\Support\ServiceProvider;
use RoachPHP\Laravel\Resolver\SpiderNamespaceRegistry;

final class CourtScraperServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->callAfterResolving(
            SpiderNamespaceRegistry::class,
            static fn (SpiderNamespaceRegistry $registry) => $registry->register('Docglyph\\CourtScraper\\Spiders'),
        );
    }
}
```

`default_spider_namespace` remains supported and is used as the final fallback namespace.

`Roach::startSpider()` and `Roach::collectSpider()` continue to expect and respect fully qualified class names.

## Laravel event bridge

By default, Roach core events are re-emitted on Laravel's event dispatcher after Symfony dispatch completes.

```php
use Illuminate\Support\Facades\Event;
use RoachPHP\Events\ItemScraped;

Event::listen(ItemScraped::class, function (ItemScraped $event): void {
    // Laravel listeners, queued listeners, and broadcasting can observe the event.
});
```

Disable forwarding with:

```dotenv
ROACH_BRIDGE_EVENTS=false
```

## Credits

- [Neurotypic AI](https://github.com/neurotypic-ai)
- [Kai Sassnowski](https://github.com/ksassnowski)
- [Roach PHP contributors](https://github.com/roach-php/laravel/contributors)

## License

MIT
