<?php


namespace Ze\JsonRPCClient\Middleware;

use Closure;
use Illuminate\Http\Request;
use Ze\JsonRPCClient\Exceptions\RPCClientException;

class IpLimitCheck
{
    public function handle($request, Closure $next)
    {
        $ipAccess = config('rpc.server.access');

        if (in_array('*', $ipAccess)) {
            return $next($request);
        }

        if (! in_array($request->getClientIp(), $ipAccess)) {
            throw new RPCClientException('ip不在白名单中', -1);
        }

        return $next($request);
    }
}