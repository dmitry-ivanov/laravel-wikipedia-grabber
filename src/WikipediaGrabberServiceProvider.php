<?php

namespace Illuminated\Wikipedia;

use Illuminate\Support\ServiceProvider;

class WikipediaGrabberServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom($this->getConfigPath(), 'wikipedia-grabber');
    }

    /**
     * Boot, when everything has been registered.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            $this->getConfigPath() => config_path('wikipedia-grabber.php'),
        ], 'config');
    }

    /**
     * Get the config path.
     *
     * @return string
     */
    protected function getConfigPath()
    {
        return __DIR__ . '/../config/wikipedia-grabber.php';
    }
}
