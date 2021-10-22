<?php


namespace Ze\JsonRpcClient\Middleware;


use Illuminate\Http\Request;
use Ze\JsonRpcClient\Exceptions\RpcClientException;

class IpLimitCheck
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $ipAccess = config('rpc.server.access');

        if(!in_array($request->getClientIp(),$ipAccess)){
            throw new RpcClientException('ip不在白名单中',-1);
        }

        return $next($request);
    }
}