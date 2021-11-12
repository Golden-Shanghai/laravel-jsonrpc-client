<?php
return [
    // 调用方配置
    'others' => [
        // 目标服务器别名
        'example' => [
            // 路由,支持域名和ip两种写法
            'host'   => 'http://127.0.0.1',
            // 端口，当使用ip时，需要配置端口
            'port'   => '8000',
            // 验签secret
            'secret' => '',
        ],
    ],
    // 服务端配置
    'server' => [
        // 验签secret
        'secret' => '',
        // ip白名单列表
        'access' => [
            '*'
        ]
    ]
];
