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
 * predis缓存驱动，适合没有安装phpredis扩展的场景
 * predis支持主从模式，支持tcp和unix模式，不支持redisCluster
 * 
 * 要求安装加载predis：https://github.com/nrk/predis
 * composer require predis/predis "1.*"
 * @author    尘缘 <130775@qq.com>
 */
class Predis
{
    protected $handler = null;
    protected $options = [
        'tcp'               => 'tcp',
        'host'              => ['tcp://127.0.0.1:6379?alias=master', 'tcp://127.0.0.1:6379?alias=slave'],
        'host'              => '127.0.0.1',
        'port'              => 6379,
        'password'          => '',
        'timeout'           => 0,
        'read_write_timeout'=> 5,
        'expire'            => 0,
        'persistent'        => false,
        'length'            => 0,
        'prefix'            => '',
        'database'          => 0,
    ];

    /**
     * 架构函数
     * @param array $options 缓存参数
     * @access public
     */
    public function __construct($options = [])
    {
        if (!class_exists('\Predis\Client')) {
            throw new Exception('_NOT_SUPPERT_:Predis');
        }
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        
        $this->handler = new \Predis\Client(
                is_array($this->options['host']) ? $this->options['host'] : $this->options,
                $this->options);
    }

    /**
     * 读取缓存
     * @access public
     * @param string $name 缓存变量名
     * @return mixed
     */
    public function get($name)
    {
        $value    = $this->handler->get($name);
        $jsonData = json_decode($value, true);
        return (null === $jsonData) ? $value : $jsonData;
    }

    /**
     * 写入缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $value  存储数据
     * @param integer $expire  有效时间（秒）
     * @return boolean
     */
    public function set($name, $value, $expire = null)
    {
        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }
        $value = (is_object($value) || is_array($value)) ? json_encode($value) : $value;
        if (is_int($expire) && $expire) {
            $result = $this->handler->setex($name, $expire, $value);
        } else {
            $result = $this->handler->set($name, $value);
        }
        return $result == 'OK';
    }

    /**
     * 删除缓存
     * @access public
     * @param string $name 缓存变量名
     * @return boolean
     */
    public function rm($name)
    {
        return $this->handler->delete($name);
    }

    /**
     * 清除缓存
     * @access public
     * @return boolean
     */
    public function clear()
    {
        return $this->handler->flushDB();
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
