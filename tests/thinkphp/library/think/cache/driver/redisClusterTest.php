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

namespace tests\thinkphp\library\think\cache\driver;

/**
 * Redisd缓存驱动测试
 * @author 尘缘 <130775@qq.com>
 */
class redisClusterTest extends redisTest
{
    private $_cacheInstance = null;

    protected function setUp()
    {
        if (!extension_loaded('redis') || !class_exists('\RedisCluster')) {
            $this->markTestSkipped("RedisCluster没有安装，已跳过测试！");
        }
    }

    protected function getCacheInstance()
    {
        if (null === $this->_cacheInstance) {
            //托管的 travis ci 尚不支持 redisCluster，暂时跳过测试
            $config = \think\Config::get('RedisCluster', []);
            if(empty($config))
                $this->_cacheInstance = new \think\cache\driver\Redis(['length' => 3]);
            else
                $this->_cacheInstance = new \think\cache\driver\RedisCluster($config);
        }
        return $this->_cacheInstance;
    }
}