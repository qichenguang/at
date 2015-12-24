<?php
return array(
	//'配置项'=>'配置值'
    'LAYOUT_ON'=>true,
    'LAYOUT_NAME'=>'layout',
    'SHOW_PAGE_TRACE' =>true,
    //'PAGE_TRACE_SAVE'=>true,
    // 添加数据库配置信息
    'DB_TYPE'   => 'mysql', // 数据库类型
    'DB_HOST'   => 'localhost', // 服务器地址
    'DB_NAME'   => 'at', // 数据库名
    'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => 'root', // 密码
    'DB_PORT'   => 3306, // 端口
    'DB_CHARSET' => 'utf8',
    'DB_PREFIX' => 'at_', // 数据库表前缀
    /*Redis设置*/
    'REDIS_HOST'		=> 'localhost', //主机
    'REDIS_PORT'		=> 6379, //端口
    'REDIS_DBNAME'          => 'appdb', //库名
    'REDIS_CTYPE'           => 2, //连接类型 1:普通连接 2:长连接
    'REDIS_TIMEOUT'         => 0, //连接超时时间(S) 0:永不超时
    //'AUTOLOAD_NAMESPACE' => array('Lib' => APP_PATH.'Lib',),
    /*Beanstalk设置*/
    'BEANSTALK_HOST'		=> '172.16.193.114', //主机
);