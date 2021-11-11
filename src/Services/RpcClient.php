<?php


namespace Ze\JsonRpcClient\Services;


use GuzzleHttp\Client;
use Ze\JsonRpcClient\Exceptions\RpcClientException;

class RpcClient
{
    protected $config;

    // 当前rpc连接
    protected $connect;

    protected $path;

    public function __construct(array $config)
    {
        $this->config = $config;

        $this->init();
    }

    public function init()
    {
        $this->connect = [];

        $this->path = '';
    }

    // 配置rpc连接
    public function connect(string $configName)
    {
        $this->connect = $this->config[$configName] ?? null;

        return $this;
    }

    // 配置rpc请求地址
    public function path(string $path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * 单次请求
     * @access public
     * @param array $method 方法名 Controller@Action
     * @param array $params 携带参数
     * @date 2021/10/21
     * @return array
     */
    public function request(string $method, array $params = [])
    {
        if (! $method) {
            throw new RpcClientException('Method is Null', -1);
        }
        $data = [
            'jsonrpc' => '2.0',
            'id'      => 1,
            'method'  => $method,
            'params'  => $params,
        ];

        return $this->send(['json' => $data]);
    }

    /**
     * 批量请求
     * @access public
     * @param array $data 参数，需包含method和params及id
     * @date 2021/10/21
     * @return
     */
    public function batchRequest(array $params = [])
    {
        $data = [];
        foreach ($params as $param) {

            if (empty($param['method']) || empty($param['params']) || empty($param['id'])) {
                throw new RpcClientException('Params Incomplete ', -1);
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

    // 执行请求
    private function send(array $data, string $type = 'single')
    {
        if (empty($this->connect['host'])) {
            throw new RpcClientException('Request url is Null', -1);
        }

        $url = $this->connect['host'] . ($this->connect['port'] ? (':' . $this->connect['port']) : '') . '/' . $this->path;

        //签名
        $data['headers'] = [
            'token' => $this->sign($data['json'])
        ];

        $response = json_decode((new Client())->request('POST', $url, $data)->getBody()->getContents(), true);

        if (isset($response['code'])) {
            throw new RpcClientException($response['message'], $response['code']);
        }

        if ($type == 'single') {
            return $response['result'];
        }

        return $response;
    }

    // 签名
    private function sign($data)
    {
        $data = ksort($data);

        return urlencode(base64_encode(hash_hmac('sha256', json_encode($data), $this->connect['secret'], true)));
    }
}
