# 基于JSON-RPC的HTTP协议RPC客户端
> 本项目基于JSON-RPC的服务端为基础实现的rpc服务端    
> server端见：https://sajya.github.io/

### 初始化
1. 发布
```shell
php artisan vendor:publish --provider="Ze\JsonRpcClient\RpcClientProvider"
```
2.修改config/rpc.php中的配置

### 示例
```php
// 注册rpc客户端
$rpc = \Rpc::connect('example')->path('api/v1/endpoint');

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
