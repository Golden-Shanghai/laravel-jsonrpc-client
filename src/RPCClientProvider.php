<?php

namespace Ze\JsonRPCClient;

use Illuminate\Support\ServiceProvider;
use Ze\JsonRPCClient\Services\RPCClient;

class RPCClientProvider extends ServiceProvider
{
    protected $routeMiddleware = [
        'rpc.auth' => \Ze\JsonRPCClient\Middleware\AuthTokenCheck::class,
        'rpc.ip'   => \Ze\JsonRPCClient\Middleware\IpLimitCheck::class,
    ];

    // 注册
    public function register()
    {
        // 服务注册
        $this->app->singleton('rpc-client', function ($app) {
            return new RPCClient($app['config']['rpc']['others']);
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
