<?php

namespace Ze\JsonRpcClient;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Ze\JsonRpcClient\Services\RpcClient;

class RpcClientProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('rpc-client', function ($app) {

            return new RpcClient($app['config']['rpc']['client']);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            // 配置文件发布
            __DIR__ . '/../config/rpc.php' => config_path('rpc.php')
        ]);

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['rpc-client'];
    }
}
