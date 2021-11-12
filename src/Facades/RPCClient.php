<?php

namespace Ze\JsonRPCClient\Facades;

use Illuminate\Support\Facades\Facade;

class RPCClient extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'rpc.client';
    }
}
