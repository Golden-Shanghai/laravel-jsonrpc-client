<?php

namespace Ze\JsonRpcClient;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Ze\JsonRpcClient\Middleware\AuthTokenCheck;
use Ze\JsonRpcClient\Middleware\IpLimitCheck;
use Ze\JsonRpcClient\Services\RpcClient;

class RpcClientProvider extends ServiceProvider implements DeferrableProvider
{
    protected $middlewareGroups = [
        'rpc'   =>  [
            'rpc.auth',
            'rpc.ip'
        ]
    ];

    protected $routeMiddleware = [
        'rpc.auth'  => AuthTokenCheck::class,
        'rpc.ip'    =>  IpLimitCheck::class,
    ];

    // 注册
    public function register()
    {
        // 服务注册
        $this->app->singleton('rpc-client', function ($app) {
            return new RpcClient($app['config']['rpc']['client']);
        });

        // 路由组
        foreach ($this->middlewareGroups as $key => $middleware) {
            app('router')->middlewareGroup($key, $middleware);
        }

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

    // 懒加载
    public function provides()
    {
        return ['rpc-client'];
    }
}
