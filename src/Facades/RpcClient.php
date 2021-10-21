<?php


namespace Ze\JsonRpcClient\Facades;


use Illuminate\Support\Facades\Facade;

class RpcClient extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'rpc-client';
    }
}
