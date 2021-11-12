# laravel JSON-RPC客户端(Http协议)
> 本项目是基于JSON-RPC的服务端端实现的rpc客户端    
> server端见：https://sajya.github.io/
***
### 初始化
1.发布
```shell
php artisan vendor:publish --provider="Ze\JsonRPCClient\RPCClientProvider"
```
2.修改config/rpc.php中的配置，可参照vendor/ze/laravel-jsonrpc-client/config/rpc.php    


3.封装了两个中间件供服务端路由调用，可参照下面的例子
```php
// rpc.id 为ip白名单验证，支持*放行所有ip
// rpc.auth 为验签中间件，必须使用
Route::rpc('/v1/rpc', [\App\Http\Procedures\TestProcedure::class])
    ->middleware(['rpc.ip','rpc.auth']);
```
### 示例
```php
// *需先启动服务

// 注册rpc客户端
$rpc = \RPC::connect('example')->path('api/v1/endpoint');

// 单条请求
$rpc->request('DataSourceProcedure@handle',['name'=>'test']);

// 批量请求
$rpc->batchRequest([
    [
        'id'     =>  1,
        'method' => 'DataSourceProcedure@handle',
        'params' => ['name'=>'test'],
    ]   
]);
```
