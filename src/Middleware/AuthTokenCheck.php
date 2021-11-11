<?php


namespace Ze\JsonRPCClient\Middleware;

use Closure;
use Illuminate\Http\Request;
use Ze\JsonRPCClient\Exceptions\RPCClientException;

class AuthTokenCheck
{
    public function handle($request, Closure $next)
    {
        $params = $request->all();

        $token = $request->header('token');

        if ($token != $this->sign($params)) {
            throw new RPCClientException('请求参数验签失败', -1);
        }

        return $next($request);
    }

    private function sign($params)
    {
        $params = ksort($params);

        $secretKey = config('rpc.server.secret');

        return urlencode(base64_encode(hash_hmac('sha256', json_encode($params), $secretKey, true)));
    }
}