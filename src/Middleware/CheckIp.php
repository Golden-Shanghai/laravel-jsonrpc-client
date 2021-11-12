<?php

// 本中间件在 RPC 端运行

namespace Ze\JsonRPCClient\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\IpUtils;
use Ze\JsonRPCClient\Exceptions\RPCClientException;

class CheckIp
{
    public function handle($request, Closure $next)
    {
        $allowIps = config('rpc.server.access');

        $ip = $request->getClientIp();

        if ($allowIps && ! IpUtils::checkIp($ip, $allowIps)) {
            throw new RPCClientException('来源 ' . $ip . ' 为非法访问');
        }

        return $next($request);
    }
}
