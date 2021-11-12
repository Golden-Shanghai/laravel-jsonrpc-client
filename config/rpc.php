<?php

return [
    // 终端配置
    'apps' => [
        // 目标服务器别名
        'example' => [
            'host' => 'http://127.0.0.1',
            'secret' => '',
        ],
    ],
    // 服务端配置
    'server' => [
        // 验签密钥
        'secret' => '',
        // IP 白名单列表
        'access' => [],
    ]
];
