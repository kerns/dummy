<?php

namespace Kerns\Dummy;

use Illuminate\Support\ServiceProvider;

final class DummyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/dummy.php', 'dummy');
    }

    public function boot(): void
    {
        $GLOBALS['dummy_path'] = rtrim($this->app['config']->get('dummy.url'), '/');
        $GLOBALS['dummy_assets_path'] = $this->app['config']->get('dummy.assets_path');
        $GLOBALS['dummy_cache_path'] = $this->app['config']->get('dummy.cache_path');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../dummy/assets' => $GLOBALS['dummy_assets_path'],
            ], 'dummy-assets');
            $this->publishes([
                __DIR__ . '/../config/dummy.php' => $this->app->configPath('dummy.php'),
            ], 'dummy-config');
        }

        // Laravel owns the request/session lifecycle, including in long-lived workers.
        $bindRequest = static function ($app, $request): void {
            Runtime::reset();
            Runtime::$query = $request->query->all();
            Runtime::$requestPath = $request->getPathInfo();
        };
        $this->app->rebinding('request', $bindRequest);
        $bindRequest($this->app, $this->app['request']);
        $this->app->terminating(static function (): void {
            Runtime::reset();
        });
    }
}
