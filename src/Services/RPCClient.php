<?php

namespace Ze\JsonRPCClient\Services;

use GuzzleHttp\Client;
use Ze\JsonRPCClient\Exceptions\RPCClientException;

class RPCClient
{
    protected $config;
    protected $connect;
    protected $path;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function connect(string $configName)
    {
        if (! isset($$this->config[$configName])) {
            throw new RPCClientException('Invalid RPC ConfigName');
        }

        $this->connect = $this->config[$configName];

        return $this;
    }

    public function path(string $path)
    {
        $this->path = $path;

        return $this;
    }

    public function request(string $method, array $params = [])
    {
        if (! $method) {
            throw new RPCClientException('Invaoid RPC Method');
        }

        $data = [
            'jsonrpc' => '2.0',
            'id'      => 1,
            'method'  => $method,
            'params'  => $params,
        ];

        return $this->send(['json' => $data]);
    }

    public function batchRequest(array $params = [])
    {
        $data = [];

        foreach ($params as $param) {

            if (empty($param['method']) || empty($param['params']) || empty($param['id'])) {
                throw new RPCClientException('Params Incomplete');
            }

            $data[] = [
                'jsonrpc' => '2.0',
                'id'      => $param['id'],
                'method'  => $param['method'],
                'params'  => $param['params'],
            ];
        }

        return $this->send(['json' => $data], 'batch');
    }

    private function send(array $payload, string $type = 'single')
    {
        if (! $this->connect['host']) {
            throw new RPCClientException('Invalid RPC Host');
        }

        $url = $this->connect['host'] . '/' . ltrim($this->path, '/');

        $payload['headers'] = [
            'sign' => $this->buildSign($payload['json']),
        ];

        $response = json_decode((new Client())->request('POST', $url, $payload)->getBody()->getContents(), true);

        // 全局异常
        if (isset($response['code'])) {
            throw new RPCClientException($response['message'], $response['code']);
        }

        // 接口自定义异常
        if (isset($response['error']) && $response['error']) {
            throw new RPCClientException($response['error']['message'] ?? '', $response['error']['code'] ?? -1);
        }

        if ($type == 'single') {
            return $response['result'];
        }

        return $response;
    }

    private function buildSign(array $params)
    {
        $params = ksort($params);

        return urlencode(base64_encode(hash_hmac('sha256', json_encode($params), $this->connect['secret'], true)));
    }
}
