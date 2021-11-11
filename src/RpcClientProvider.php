<?php

namespace Ze\JsonRpcClient;

use Illuminate\Support\ServiceProvider;
use Ze\JsonRpcClient\Services\RpcClient;

class RpcClientProvider extends ServiceProvider
{
    protected $routeMiddleware = [
        'rpc.auth' => \Ze\JsonRpcClient\Middleware\AuthTokenCheck::class,
        'rpc.ip'   => \Ze\JsonRpcClient\Middleware\IpLimitCheck::class,
    ];

    // 注册
    public function register()
    {
        // 服务注册
        $this->app->singleton('rpc-client', function ($app) {
            return new RpcClient($app['config']['rpc']['client']);
        });

        // 路由中间件注册
        foreach ($this->routeMiddleware as $key => $middleware) {
            app('router')->aliasMiddleware($key, $middleware);
        }
    }

    // 引导
    public function boot()
    {
        $this->publishes([
            // 配置文件发布
            __DIR__ . '/../config/rpc.php' => config_path('rpc.php')
        ]);

    }
}
