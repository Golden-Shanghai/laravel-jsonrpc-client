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
        if (! isset($this->config[$configName])) {
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

            if (empty($param['method']) || empty($param['id'])) {
                throw new RPCClientException('Params Incomplete');
            }

            $data[] = [
                'jsonrpc' => '2.0',
                'id'      => $param['id'],
                'method'  => $param['method'],
                'params'  => $param['params'] ?? [],
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

        $resp = (new Client())->request('POST', $url, $payload)->getBody()->getContents();

        $json = json_decode($resp, true);

        if ($json === null) {
            throw new RPCClientException('Invalid JsonResp: ' . $resp);
        }

        // 全局异常
        if (isset($json['code'])) {
            throw new RPCClientException($json['message'], $json['code']);
        }

        // 接口自定义异常
        if (isset($json['error']) && $json['error']) {
            throw new RPCClientException($json['error']['message'] ?? '', $json['error']['code'] ?? -1);
        }

        if ($type == 'single') {
            return $json['result'];
        }

        return $json;
    }

    private function buildSign(array $params)
    {
        ksort($params);

        return urlencode(base64_encode(hash_hmac('sha256', json_encode($params), $this->connect['secret'], true)));
    }
}