<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace think\cache\driver;

use think\Cache;
use think\Exception;

/**
 * RedisCluster缓存驱动
 * 
 * 要求安装phpredis扩展：https://github.com/nicolasff/phpredis
 * @author    尘缘 <130775@qq.com>
 * @example   https://github.com/phpredis/phpredis/blob/develop/cluster.markdown
 */
class RedisCluster
{
    protected $handler = null;
    protected $options = [
        'host'         => ['127.0.0.1:7000', '127.0.0.1:7001', '127.0.0.1:7002'],
        'password'     => '',
        'timeout'      => 10,
        'read_timeout' => 10,
        'expire'       => 0,
        'persistent'   => false,
        'length'       => 0,
        'prefix'       => '',
        'serialize'  => \Redis::SERIALIZER_PHP,
    ];

    /**
     * 架构函数
     * @param  array $options 缓存参数
     * @access public
     */
    public function __construct($options = [])
    {
        if (!extension_loaded('redis') || !class_exists('\RedisCluster')) {
            throw new Exception('_NOT_SUPPERT_:RedisCluster');
        }

        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        
        try {
            $this->handler = new \RedisCluster(
                null, $this->options['host'], $this->options['timeout'],
                $this->options['read_timeout'], $this->options['persistent']
            );
        } catch (Exception $e) {
             throw new Exception($e->getMessage());
        }
        
        // Always distribute readonly commands between masters and slaves, at random
        $this->handler->setOption(
            \RedisCluster::OPT_SLAVE_FAILOVER, \RedisCluster::FAILOVER_DISTRIBUTE
        );
        
        $this->handler->setOption(\Redis::OPT_SERIALIZER, $this->options['serialize']);
        if(strlen($this->options['prefix'])) {
            $this->handler->setOption(\Redis::OPT_PREFIX, $this->options['prefix']);
        }

        //Cluster already disables having multiple "databases,"
        //so disabling another server-level feature seems acceptable too.
        if ('' != $this->options['password']) {
            $this->handler->auth($this->options['password']);
        }
    }

    /**
     * 读取缓存
     * @access public
     * @param  string $name    缓存变量名
     * @return mixed
     */
    public function get($name)
    {
        return $this->handler->get($name);
    }

    /**
     * 写入缓存
     * @access public
     * @param  string  $name    缓存变量名
     * @param  mixed   $value   存储数据
     * @param  integer $expire  有效时间（秒）
     * @return boolean
     */
    public function set($name, $value, $expire = null)
    {
        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }
        if (is_int($expire) && $expire) {
            $result = $this->handler->setex($name, $expire, $value);
        } else {
            $result = $this->handler->set($name, $value);
        }
        return $result;
    }

    /**
     * 删除缓存
     * @access public
     * @param  string   $name   缓存变量名
     * @return boolean
     */
    public function rm($name)
    {
        return $this->handler->del($name);
    }

    /**
     * 清除缓存
     * @access public
     * @return boolean
     */
    public function clear()
    {
        return $this->handler->flushall("");
    }

    /**
     * 返回句柄对象，可执行其它高级方法
     *
     * @access public
     * @return object
     */
    public function handler()
    {
        return $this->handler;
    }
}
