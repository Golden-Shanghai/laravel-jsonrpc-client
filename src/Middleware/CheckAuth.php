<?php

// 本中间件在 RPC 端运行

namespace Ze\JsonRPCClient\Middleware;

use Closure;
use Illuminate\Http\Request;
use Ze\JsonRPCClient\Exceptions\RPCClientException;

class CheckAuth
{
    public function handle($request, Closure $next)
    {
        $params = $request->all();

        $sign = $request->header('sign');

        if ($sign != $this->buildSign($params)) {
            throw new RPCClientException('RPC 请求参数验签失败');
        }

        return $next($request);
    }

    private function buildSign($params)
    {
        $params = ksort($params);

        $secretKey = config('rpc.server.secret');

        return urlencode(base64_encode(hash_hmac('sha256', json_encode($params), $secretKey, true)));
    }
}
