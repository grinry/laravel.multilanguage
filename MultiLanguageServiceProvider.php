<?php
/**
 * @author Rytis Grincevicius <rytis@kiberzauras.com>
 * @license MIT
 */
namespace Kiberzauras\MultiLanguage;

use Illuminate\Support\ServiceProvider;
use App;

class MultiLanguageServiceProvider extends ServiceProvider
{

    /**
     * Set our created language constant to laravel locale
     */
    public function boot()
    {
        defined('Language') || define('Language', config('app.locale'));
        App::setLocale(Language);
    }

    public function register()
    {
        $this->registerUrlGenerator();
    }

    /**
     * @return array
     */
    public function provides()
    {
        return [
            'url',
        ];
    }

    /**
     *  Overwriting laravel urlGenerator
     */
    protected function registerUrlGenerator()
    {
        $this->app['url'] = $this->app->share(function ($app) {
            $routes = $app['router']->getRoutes();

            // The URL generator needs the route collection that exists on the router.
            // Keep in mind this is an object, so we're passing by references here
            // and all the registered routes will be available to the generator.
            $app->instance('routes', $routes);

            $url = new UrlGenerator(
                $routes, $app->rebinding(
                    'request', $this->requestRebinder()
                )
            );

            $url->setSessionResolver(function () {
                return $this->app['session'];
            });

            // If the route collection is "rebound", for example, when the routes stay
            // cached for the application, we will need to rebind the routes on the
            // URL generator instance so it has the latest version of the routes.
            $app->rebinding('routes', function ($app, $routes) {
                $app['url']->setRoutes($routes);
            });

            return $url;
        });
    }

    /**
     * @return \Closure
     */
    protected function requestRebinder()
    {
        return function ($app, $request) {
            $app['url']->setRequest($request);
        };
    }
}
