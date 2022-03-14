<?php

namespace Illuminated\Wikipedia;

use Illuminate\Support\ServiceProvider;

class WikipediaGrabberServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom($this->getConfigPath(), 'wikipedia-grabber');
    }

    /**
     * Boot, when everything has been registered.
     */
    public function boot(): void
    {
        $this->publishes([
            $this->getConfigPath() => config_path('wikipedia-grabber.php'),
        ], 'config');
    }

    /**
     * Get the config path.
     */
    protected function getConfigPath(): string
    {
        return __DIR__ . '/../config/wikipedia-grabber.php';
    }
}
