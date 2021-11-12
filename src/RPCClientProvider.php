<?php

namespace Ze\JsonRPCClient;

use Illuminate\Support\ServiceProvider;
use Ze\JsonRPCClient\Services\RPCClient;

class RPCClientProvider extends ServiceProvider
{
    protected $routeMiddleware = [
        'rpc.auth' => \Ze\JsonRPCClient\Middleware\CheckAuth::class,
        'rpc.ip'   => \Ze\JsonRPCClient\Middleware\CheckIp::class,
    ];

    public function register()
    {
        $this->app->singleton('rpc.client', function ($app) {
            return new RPCClient($app['config']['rpc']['apps']);
        });

        foreach ($this->routeMiddleware as $key => $middleware) {
            app('router')->aliasMiddleware($key, $middleware);
        }
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/rpc.php' => config_path('rpc.php'),
        ]);
    }
}
