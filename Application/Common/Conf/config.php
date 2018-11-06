<?php
return array(
	 /* 数据库设置*/
    'URL_MODEL'=>0,
    'DB_TYPE' => 'mysqli', // 数据库类型
    'DB_HOST' => '127.0.0.1', // 服务器地址
    'DB_NAME' => 'aitt', // 数据库名
    'DB_USER' => 'root', // 用户名
    'DB_PWD' => '123456@!testS', // 密码
    'DB_PORT' => '3306', // 端口
    'DB_PREFIX' => 'aitt_', // 数据库表前缀

    /* 站点安全设置 */
    "AUTHCODE" => 'AuthkeyCODEAITT', //密钥

    /* Cookie设置 */
    "COOKIE_PREFIX" => 'AITT_', //Cookie前缀

    /* 数据缓存设置 */
    'DATA_CACHE_PREFIX' => 'DAITT_', // 缓存前缀

    'TMPL_PARSE_STRING' =>  array(

    '__IMAGES__'    =>  __ROOT__.'/Public/images',
    '__CSS__'       =>  __ROOT__.'/Public/css',
    '__JS__'        =>  __ROOT__.'/Public/js',
    '__COMMON__' =>  __ROOT__.'/Public/common'
),
);