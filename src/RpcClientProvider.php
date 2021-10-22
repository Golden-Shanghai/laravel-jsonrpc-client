<?php

namespace Ze\JsonRpcClient;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Ze\JsonRpcClient\Middleware\AuthTokenCheck;
use Ze\JsonRpcClient\Middleware\IpLimitCheck;
use Ze\JsonRpcClient\Services\RpcClient;

class RpcClientProvider extends ServiceProvider implements DeferrableProvider
{

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

        // 路由中间件注册
        foreach ($this->routeMiddleware as $key => $middleware) {
            $this->addMiddlewareAlias($key, $middleware);
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


    protected function addMiddlewareAlias($name, $class)
    {
        $router = $this->app['router'];

        // 判断aliasMiddleware是否在类中存在
        if (method_exists($router, 'aliasMiddleware')) {
            // aliasMiddleware 顾名思义,就是给中间件设置一个别名
            return $router->aliasMiddleware($name, $class);
        }

        return $router->middleware($name, $class);
    }
}
